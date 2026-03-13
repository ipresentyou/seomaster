<?php

namespace App\Http\Controllers\Seo;

use App\Models\SeoProject;
use App\Services\StorefrontScraper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductSeoController extends BaseSeoController
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
            $search       = $request->input('search', '');

            $meta = $this->buildPageMeta($selectedSc, $selectedLang);
            if (! $selectedSc)   $selectedSc   = array_key_first($meta['salesChannels']) ?? '';
            if (! $selectedLang) $selectedLang = array_key_first($meta['domains'][$selectedSc] ?? []) ?? '';

            $rows = [];
            if ($selectedSc && $selectedLang) {
                $rawProducts = $this->shopware->getProducts($selectedLang, $limit, $search);
                $productIds  = array_column($rawProducts, 'id');
                $seoUrls     = $productIds ? $this->shopware->getSeoUrls($productIds, $selectedSc, $selectedLang) : [];
                $base        = $meta['domains'][$selectedSc][$selectedLang]['url'] ?? '';

                foreach ($rawProducts as $prod) {
                    $a = $prod;
                    $rows[] = [
                        'id'            => $prod['id'],
                        'name'          => $a['translated']['name']            ?? $a['name']            ?? '',
                        'productNumber' => $a['productNumber']                 ?? '',
                        'title'         => $a['translated']['metaTitle']       ?? $a['metaTitle']       ?? '',
                        'metaDesc'      => $a['translated']['metaDescription'] ?? $a['metaDescription'] ?? '',
                        'description'   => $a['translated']['description']     ?? $a['description']     ?? '',
                    'keywords'      => $a['translated']['keywords']          ?? $a['keywords']          ?? '',
                    'url'           => isset($seoUrls[$prod['id']]) ? $base . '/' . ltrim($seoUrls[$prod['id']], '/') : '',
                ];
            }
        }

        return view('seo.products.index', array_merge($meta, compact(
            'project', 'rows', 'selectedSc', 'selectedLang', 'limit', 'search'
        )));
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Connection timeout or network error
            return view('seo.products.index', [
                'project' => $project,
                'rows' => [],
                'selectedSc' => $selectedSc ?? '',
                'selectedLang' => $selectedLang ?? '',
                'limit' => $limit ?? 50,
                'search' => $search ?? '',
                'connectionError' => 'Verbindung zum Shopware-Shop fehlgeschlagen. Bitte überprüfen Sie, ob der Shop erreichbar ist und die API-Zugangsdaten korrekt sind.',
                'languages' => [],
                'domainName' => $project->name ?? '',
                'salesChannels' => [],
                'domains' => [],
                'storefrontUrl' => ''
            ]);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            // HTTP errors (401, 403, 500, etc.)
            $errorMessage = 'API-Anfrage fehlgeschlagen';
            if ($e->response->status() === 401) {
                $errorMessage = 'API-Anmeldung fehlgeschlagen. Bitte überprüfen Sie die Shopware API-Zugangsdaten.';
            } elseif ($e->response->status() === 403) {
                $errorMessage = 'Keine Berechtigung für diese Shopware-API-Ressource.';
            } elseif ($e->response->status() >= 500) {
                $errorMessage = 'Shopware-Serverfehler. Bitte versuchen Sie es später erneut.';
            }
            
            return view('seo.products.index', [
                'project' => $project,
                'rows' => [],
                'selectedSc' => $selectedSc ?? '',
                'selectedLang' => $selectedLang ?? '',
                'limit' => $limit ?? 50,
                'search' => $search ?? '',
                'connectionError' => $errorMessage,
                'languages' => [],
                'domainName' => $project->name ?? '',
                'salesChannels' => [],
                'domains' => [],
                'storefrontUrl' => ''
            ]);
        } catch (\Exception $e) {
            // Other errors
            return view('seo.products.index', [
                'project' => $project,
                'rows' => [],
                'selectedSc' => $selectedSc ?? '',
                'selectedLang' => $selectedLang ?? '',
                'limit' => $limit ?? 50,
                'search' => $search ?? '',
                'connectionError' => 'Fehler beim Laden der Produkte: ' . $e->getMessage(),
                'languages' => [],
                'domainName' => $project->name ?? '',
                'salesChannels' => [],
                'domains' => [],
                'storefrontUrl' => ''
            ]);
        }
    }

    // ── Analyze storefront page ───────────────────────────────────────────────

    public function analyze(Request $request, SeoProject $project): JsonResponse
    {
        $this->bootProject($project);
        $url = $request->input('url', '');

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return $this->err('Ungültige URL');
        }

        return $this->ok($this->scraper->scrapeProduct($url));
    }

    // ── AI Generate ───────────────────────────────────────────────────────────

    public function generate(Request $request, SeoProject $project): JsonResponse
    {
        $this->bootProject($project);

        $v = $request->validate([
            'name'               => 'required|string|max:255',
            'productNumber'      => 'nullable|string',
            'content'            => 'nullable|string',
            'h1'                 => 'nullable|string',
            'price'              => 'nullable|string',
            'features'           => 'nullable|string',
            'customInstructions' => 'nullable|string|max:3000',
            'targetLang'         => 'nullable|string',
            'domain'             => 'nullable|string',
            'generate'           => 'required|array',
            'generate.*'         => 'in:title,desc,text',
        ]);

        $result = [];
        $tokens = 0;

        try {
            if (in_array('title', $v['generate']) || in_array('desc', $v['generate'])) {
                $meta = $this->ai->generateMeta(
                    entityName:         $v['name'],
                    entityType:         'product',
                    pageContent:        $v['content']  ?? '',
                    h1:                 $v['h1']        ?? '',
                    targetLanguage:     $v['targetLang'] ?? '',
                    storefrontDomain:   $v['domain']   ?? '',
                    customInstructions: $v['customInstructions'] ?? '',
                    extra: array_filter([
                        'productNumber' => $v['productNumber'] ?? '',
                        'price'         => $v['price']         ?? '',
                        'features'      => $v['features']      ?? '',
                    ]),
                );
                if (isset($meta['error'])) return $this->err($meta['error']);
                if (! in_array('title', $v['generate'])) unset($meta['title']);
                if (! in_array('desc',  $v['generate'])) unset($meta['metaDesc']);
                $result = array_merge($result, $meta);
                $tokens += 400;
            }

            if (in_array('text', $v['generate'])) {
                $seo = $this->ai->generateSeoText(
                    entityName:         $v['name'],
                    entityType:         'product',
                    pageContent:        $v['content']  ?? '',
                    h1:                 $v['h1']        ?? '',
                    targetLanguage:     $v['targetLang'] ?? '',
                    customInstructions: $v['customInstructions'] ?? '',
                    extra: array_filter([
                        'productNumber' => $v['productNumber'] ?? '',
                        'price'         => $v['price']         ?? '',
                        'features'      => $v['features']      ?? '',
                    ]),
                );
                if (isset($seo['error'])) return $this->err($seo['error']);
                $result = array_merge($result, $seo);
                $tokens += 1000;
            }
        } catch (\RuntimeException $e) {
            return $this->err($e->getMessage());
        }

        $this->log('meta.generated', 'product', '', [], $tokens);

        return $this->ok($result);
    }

    // ── Save to Shopware ──────────────────────────────────────────────────────

    public function save(Request $request, SeoProject $project): JsonResponse
    {
        $this->bootProject($project);

        $v = $request->validate([
            'productId' => 'required|string',
            'langId'    => 'required|string',
            'title'     => 'nullable|string|max:255',
            'metaDesc'  => 'nullable|string|max:500',
            'seoText'   => 'nullable|string',
        ]);

        $payload = array_filter([
            'metaTitle'       => strip_tags($v['title']    ?? ''),
            'metaDescription' => strip_tags($v['metaDesc'] ?? ''),
            'keywords'        => strip_tags($v['keywords'] ?? ''),
            'description'     => $v['seoText'] ?? '',
        ], fn($val) => $val !== '');

        $ok = $this->shopware->saveProduct($v['productId'], $v['langId'], $payload);

        if ($ok) {
            $this->log('meta.saved', 'product', $v['productId'], $payload);
        }

        return $ok ? $this->ok() : $this->err('Shopware PATCH fehlgeschlagen', 500);
    }
}
