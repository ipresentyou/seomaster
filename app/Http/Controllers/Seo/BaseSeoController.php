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
    protected AiService $ai;

    // ── Boot ──────────────────────────────────────────────────────────────────

    protected function bootProject(SeoProject $project): void
    {
        // Authorize: project must belong to the authenticated user
        abort_unless($project->user_id === auth()->id(), 403);

        $this->project  = $project;
        $this->shopware = ShopwareService::forProject($project);
        $this->ai       = AiService::forUser(auth()->id());
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

    // ── Shared page-data builder ──────────────────────────────────────────────

    /**
     * Normalise Shopware language map + domain map for the index views.
     */
    protected function buildPageMeta(string $selectedSc, string $selectedLang): array
    {
        $languages    = $this->shopware->getLanguages();
        $salesChannels = $this->shopware->getSalesChannels();
        $domains      = $this->shopware->getDomains();

        // Resolve storefront base URL for the selected language
        $storefrontUrl = $domains[$selectedSc][$selectedLang]['url'] ?? '';
        $domainName    = parse_url($storefrontUrl, PHP_URL_HOST) ?: 'Your Store';
        $domainName    = preg_replace('/^www\./', '', $domainName);

        return compact('languages', 'salesChannels', 'domains', 'storefrontUrl', 'domainName');
    }
}
