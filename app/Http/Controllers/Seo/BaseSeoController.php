<?php

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Models\SeoActivityLog;
use App\Models\SeoProject;
use App\Services\AiService;
use App\Services\ShopwareService;
use Illuminate\Http\JsonResponse;

abstract class BaseSeoController extends Controller
{
    protected SeoProject $project;
    protected ShopwareService $shopware;
    private ?AiService $ai = null;

    // ── Boot ──────────────────────────────────────────────────────────────────

    protected function bootProject(SeoProject $project): void
    {
        // Authorize: project must belong to the authenticated user
        abort_unless($project->user_id === auth()->id(), 403);

        $this->project  = $project;
        $this->shopware = ShopwareService::forProject($project);
        // AI is initialized lazily via getAi() — avoids 500 on pages that don't need it
    }

    protected function getAi(): AiService
    {
        return $this->ai ??= AiService::forUser(auth()->id());
    }

    // ── Activity Log ──────────────────────────────────────────────────────────

    protected function log(
        string $action,
        string $entityType,
        string $entityId,
        array  $payload = [],
        int    $tokens  = 0
    ): void {
        SeoActivityLog::record(
            userId:     auth()->id(),
            projectId:  $this->project->id,
            action:     $action,
            entityType: $entityType,
            entityId:   $entityId,
            payload:    $payload,
            tokens:     $tokens,
        );
    }

    // ── JSON helpers ──────────────────────────────────────────────────────────

    protected function ok(array $data = []): JsonResponse
    {
        return response()->json(array_merge(['success' => true], $data));
    }

    protected function err(string $message, int $status = 422): JsonResponse
    {
        return response()->json(['success' => false, 'error' => $message], $status);
    }

    /**
     * Heuristic for picking a German vs. English default AI prompt template
     * based on the selected Shopware language's display name.
     */
    protected function isGermanLanguage(string $langName): bool
    {
        return str_contains(mb_strtolower($langName), 'deutsch') || str_contains(mb_strtolower($langName), 'german');
    }

    /**
     * Builds the per-language "already optimized?" status list for one row,
     * so the list view can show at a glance which configured storefront
     * languages (e.g. DE/EN) still need Meta-Title/-Description.
     *
     * @param array<string> $domainLangIds  All language IDs with a storefront domain for this sales channel.
     * @param array<string,array{hasTitle:bool,hasDesc:bool}> $otherStatuses  Precomputed status for this entity, keyed by langId (excluding the currently selected language).
     */
    protected function buildLangStatus(
        string $selectedLang,
        bool $hasTitle,
        bool $hasDesc,
        array $otherStatuses,
        array $languageNames,
        array $domainLangIds
    ): array {
        $statuses = [];
        foreach ($domainLangIds as $langId) {
            $statuses[$langId] = $langId === $selectedLang
                ? ['langId' => $langId, 'name' => $languageNames[$langId] ?? $langId, 'hasTitle' => $hasTitle, 'hasDesc' => $hasDesc, 'isCurrent' => true]
                : ['langId' => $langId, 'name' => $languageNames[$langId] ?? $langId, 'hasTitle' => $otherStatuses[$langId]['hasTitle'] ?? false, 'hasDesc' => $otherStatuses[$langId]['hasDesc'] ?? false, 'isCurrent' => false];
        }

        return $statuses;
    }

    /**
     * Custom AI prompt to show/use for the selected language. The legacy,
     * pre-multilingual seo_prompt field only applies as a fallback for German —
     * it was almost always written in that context (often with German-domain-
     * specific text baked in), so silently reusing it for every other language
     * would leak German instructions into non-German generations.
     */
    protected function customPromptFor(SeoProject $project, string $langId, bool $isGerman): ?string
    {
        if (array_key_exists($langId, $project->seo_prompts ?? [])) {
            return $project->seo_prompts[$langId] ?: null;
        }

        return $isGerman ? $project->seo_prompt : null;
    }

    /**
     * Sorts an assembled row array (products/categories) by name, product
     * number, or "missing field first" — done in PHP since the underlying
     * Shopware search API only sorts by name.
     */
    protected function sortRows(array $rows, string $sort): array
    {
        usort($rows, fn ($a, $b) => match ($sort) {
            'name_desc'     => strcasecmp($b['name'] ?? '', $a['name'] ?? ''),
            'number_asc'    => strcasecmp($a['productNumber'] ?? '', $b['productNumber'] ?? ''),
            'missing_title' => (int) (! empty($a['title']))    <=> (int) (! empty($b['title'])),
            'missing_desc'  => (int) (! empty($a['metaDesc'])) <=> (int) (! empty($b['metaDesc'])),
            default         => strcasecmp($a['name'] ?? '', $b['name'] ?? ''),
        });

        return $rows;
    }

    /**
     * Runs an AJAX action and guarantees a JSON response even on failure —
     * without this, an uncaught exception (e.g. an expired Shopware token)
     * renders Laravel's HTML error page, which breaks the frontend's
     * `res.json()` call ("unexpected character at line 1 column 1").
     */
    protected function guardJson(\Closure $fn): JsonResponse
    {
        try {
            return $fn();
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->err(collect($e->errors())->flatten()->first() ?? 'Ungültige Eingabe.', 422);
        } catch (\Illuminate\Http\Client\ConnectionException) {
            return $this->err('Verbindung zum Shop fehlgeschlagen. Bitte später erneut versuchen.', 502);
        } catch (\RuntimeException $e) {
            return $this->err($e->getMessage());
        } catch (\Throwable $e) {
            report($e);
            return $this->err('Unerwarteter Fehler: ' . $e->getMessage(), 500);
        }
    }

    // ── Shared page-data builder ──────────────────────────────────────────────

    /**
     * Normalise Shopware language map + domain map for the index views.
     */
    protected function buildPageMeta(string $selectedSc, string $selectedLang): array
    {
        $languages    = $this->shopware->getLanguages();
        $salesChannels = $this->shopware->getSalesChannels();
        $domains      = $this->shopware->getDomains();

        // Auto-select first SC/lang if none given (for initial page load)
        $sc   = $selectedSc   ?: (string) array_key_first($salesChannels ?? []);
        $lang = $selectedLang ?: (string) array_key_first($domains[$sc] ?? []);

        $storefrontUrl = $domains[$sc][$lang]['url'] ?? '';
        $domainName    = parse_url($storefrontUrl, PHP_URL_HOST) ?: 'Your Store';
        $domainName    = preg_replace('/^www\./', '', $domainName);

        return compact('languages', 'salesChannels', 'domains', 'storefrontUrl', 'domainName');
    }
}
