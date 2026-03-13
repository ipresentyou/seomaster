<?php

namespace App\Http\Controllers\Seo;

use App\Models\SeoProject;
use App\Services\StorefrontScraper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategorySeoController extends BaseSeoController
{
    public function __construct(private StorefrontScraper $scraper) {}

    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request, SeoProject $project)
    {
        try {
            $this->bootProject($project);

            $selectedSc   = $request->input('sc', '');
            $selectedLang = $request->input('lang', '');
            $limit        = (int) $request->input('max', 50);

            $meta = $this->buildPageMeta($selectedSc, $selectedLang);
            $salesChannels = $meta['salesChannels'];

            if (! $selectedSc) $selectedSc   = array_key_first($salesChannels) ?? '';
            if (! $selectedLang) $selectedLang = array_key_first($meta['domains'][$selectedSc] ?? []) ?? '';

            $rows = [];
            $storefrontDomain = $meta['domains'][$selectedSc][$selectedLang]['url'] ?? '';
            if ($selectedSc && $selectedLang && isset($salesChannels[$selectedSc])) {
                $navRootId   = $salesChannels[$selectedSc]['navigationCategoryId'] ?? '';
                $rawCategories = $navRootId ? $this->shopware->getCategories($selectedLang, $navRootId, $limit) : [];
                $categoryIds = array_column($rawCategories, 'id');
                $seoUrls     = $categoryIds ? $this->shopware->getSeoUrls($categoryIds, $selectedSc, $selectedLang) : [];
                $base        = $meta['domains'][$selectedSc][$selectedLang]['url'] ?? '';
                $storefrontDomain = $base;

            foreach ($rawCategories as $cat) {
                $a = $cat;
                $rows[] = [
                    'id'       => $cat['id'],
                    'name'     => $a['translated']['name']              ?? $a['name']              ?? '',
                    'title'    => $a['translated']['metaTitle']         ?? $a['metaTitle']         ?? '',
                    'metaDesc' => $a['translated']['metaDescription']   ?? $a['metaDescription']   ?? '',
                    'keywords' => $a['translated']['keywords']          ?? $a['keywords']          ?? '',
                    'description' => $a['translated']['description']   ?? $a['description']       ?? '',
                    'type'     => $a['type'] ?? 'page',
                    'url'      => isset($seoUrls[$cat['id']]) ? $base . '/' . ltrim($seoUrls[$cat['id']], '/') : '',
                ];
            }
        }

        return view('seo.categories.index', array_merge($meta, compact(
            'project', 'rows', 'selectedSc', 'selectedLang', 'limit', 'salesChannels', 'storefrontDomain'
        )));
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Connection timeout or network error
            return view('seo.categories.index', [
                'project' => $project,
                'rows' => [],
                'selectedSc' => $selectedSc ?? '',
                'selectedLang' => $selectedLang ?? '',
                'limit' => $limit ?? 50,
                'salesChannels' => [],
                'storefrontDomain' => '',
                'connectionError' => 'Verbindung zum Shopware-Shop fehlgeschlagen. Bitte überprüfen Sie, ob der Shop erreichbar ist und die API-Zugangsdaten korrekt sind.',
                'languages' => [],
                'domainName' => $project->name ?? ''
            ]);
        } catch (\Exception $e) {
            // Other errors
            return view('seo.categories.index', [
                'project' => $project,
                'rows' => [],
                'selectedSc' => $selectedSc ?? '',
                'selectedLang' => $selectedLang ?? '',
                'limit' => $limit ?? 50,
                'salesChannels' => [],
                'storefrontDomain' => '',
                'connectionError' => 'Fehler beim Laden der Kategorien: ' . $e->getMessage(),
                'languages' => [],
                'domainName' => $project->name ?? ''
            ]);
        }
    }

    // ── Analyze ───────────────────────────────────────────────────────────────

    public function analyze(Request $request, SeoProject $project): JsonResponse
    {
        $this->bootProject($project);
        $url = $request->input('url', '');

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return $this->err('Ungültige URL');
        }

        return $this->ok($this->scraper->scrapeCategory($url));
    }

    // ── AI Generate ───────────────────────────────────────────────────────────

    public function generate(Request $request, SeoProject $project): JsonResponse
    {
        $this->bootProject($project);

        $v = $request->validate([
            'name'               => 'required|string|max:255',
            'content'            => 'nullable|string',
            'h1'                 => 'nullable|string',
            'existingKeywords'   => 'nullable|string',
            'customInstructions' => 'nullable|string|max:3000',
            'targetLang'         => 'nullable|string',
            'domain'             => 'nullable|string',
            'generate'           => 'required|array',
            'generate.*'         => 'in:title,desc,keywords,text',
        ]);

        $result = [];
        $tokens = 0;

        try {
            if (in_array('title', $v['generate']) || in_array('desc', $v['generate']) || in_array('keywords', $v['generate'])) {
                $meta = $this->ai->generateMeta(
                    entityName:         $v['name'],
                    entityType:         'category',
                    pageContent:        $v['content']          ?? '',
                    h1:                 $v['h1']               ?? '',
                    targetLanguage:     $v['targetLang']       ?? '',
                    storefrontDomain:   $v['domain']           ?? '',
                    customInstructions: $v['customInstructions'] ?? '',
                    existingKeywords:   $v['existingKeywords'] ?? '',
                );
                if (isset($meta['error'])) return $this->err($meta['error']);
                if (! in_array('title',    $v['generate'])) unset($meta['title']);
                if (! in_array('desc',     $v['generate'])) unset($meta['metaDesc']);
                if (! in_array('keywords', $v['generate'])) unset($meta['keywords']);
                $result = array_merge($result, $meta);
                $tokens += 400;
            }

            if (in_array('text', $v['generate'])) {
                $seo = $this->ai->generateSeoText(
                    entityName:         $v['name'],
                    entityType:         'category',
                    pageContent:        $v['content']          ?? '',
                    h1:                 $v['h1']               ?? '',
                    targetLanguage:     $v['targetLang']       ?? '',
                    customInstructions: $v['customInstructions'] ?? '',
                );
                if (isset($seo['error'])) return $this->err($seo['error']);
                $result = array_merge($result, $seo);
                $tokens += 1200;
            }
        } catch (\RuntimeException $e) {
            return $this->err($e->getMessage());
        }

        $this->log('meta.generated', 'category', '', [], $tokens);

        return $this->ok($result);
    }

    // ── Save ──────────────────────────────────────────────────────────────────

    public function save(Request $request, SeoProject $project): JsonResponse
    {
        $this->bootProject($project);

        $v = $request->validate([
            'categoryId' => 'required|string',
            'langId'     => 'required|string',
            'title'      => 'nullable|string|max:255',
            'metaDesc'   => 'nullable|string|max:500',
            'keywords'   => 'nullable|string|max:500',
            'seoText'    => 'nullable|string',
        ]);

        $payload = array_filter([
            'metaTitle'       => strip_tags($v['title']    ?? ''),
            'metaDescription' => strip_tags($v['metaDesc'] ?? ''),
            'keywords'        => strip_tags($v['keywords'] ?? ''),
            'description'     => $v['seoText'] ?? '',
        ], fn($val) => $val !== '');

        error_log("PATCH payload: " . json_encode($v) . " payload: " . json_encode($payload));
        $ok = $this->shopware->saveCategory($v['categoryId'], $v['langId'], $payload);

        if ($ok) {
            $this->log('meta.saved', 'category', $v['categoryId'], $payload);
        }

        return $ok ? $this->ok() : $this->err('Shopware PATCH fehlgeschlagen', 500);
    }

    public function savePrompt(Request $request, SeoProject $project): JsonResponse
    {
        $v = $request->validate(['prompt' => 'nullable|string|max:5000']);
        $project->update(['seo_prompt' => $v['prompt'] ?? '']);
        return response()->json(['success' => true]);
    }
}
