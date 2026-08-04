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
            $search       = (string) $request->input('search', '');
            $sort         = (string) $request->input('sort', 'name_asc');

            $meta = $this->buildPageMeta($selectedSc, $selectedLang);
            if (! $selectedSc)   $selectedSc   = array_key_first($meta['salesChannels']) ?? '';
            if (! $selectedLang) $selectedLang = array_key_first($meta['domains'][$selectedSc] ?? []) ?? '';

            $rows = [];
            if ($selectedSc && $selectedLang) {
                $rawProducts = $this->shopware->getProducts($selectedLang, $limit, $search);
                $productIds  = array_column($rawProducts, 'id');
                $seoUrls     = $productIds ? $this->shopware->getSeoUrls($productIds, $selectedSc, $selectedLang) : [];
                $base        = $meta['domains'][$selectedSc][$selectedLang]['url'] ?? '';

                // Für alle anderen Sprachen dieses Sales Channels den Optimierungsstatus
                // nachladen, damit die Liste "✅ DE optimiert / ⚠️ EN fehlt" anzeigen kann.
                $domainLangIds = array_keys($meta['domains'][$selectedSc] ?? []);
                $otherLangStatuses = [];
                foreach ($domainLangIds as $otherLangId) {
                    if ($otherLangId === $selectedLang) continue;
                    $otherRaw = $this->shopware->getProducts($otherLangId, $limit, $search);
                    foreach ($otherRaw as $p) {
                        $b = $p;
                        $otherLangStatuses[$p['id']][$otherLangId] = [
                            'hasTitle' => ! empty($b['translated']['metaTitle']       ?? $b['metaTitle']       ?? ''),
                            'hasDesc'  => ! empty($b['translated']['metaDescription'] ?? $b['metaDescription'] ?? ''),
                        ];
                    }
                }

                foreach ($rawProducts as $prod) {
                    $a = $prod;
                    $productNumber = $a['productNumber'] ?? '';
                    // Manche Produkte haben keinen übersetzten Namen — Produktnummer als Fallback,
                    // damit weder die Liste noch die KI-Generierung mit einem leeren Titel dastehen.
                    $name  = ($a['translated']['name'] ?? $a['name'] ?? '') ?: $productNumber;
                    $title = $a['translated']['metaTitle']       ?? $a['metaTitle']       ?? '';
                    $desc  = $a['translated']['metaDescription'] ?? $a['metaDescription'] ?? '';
                    $rows[] = [
                        'id'            => $prod['id'],
                        'name'          => $name,
                        'productNumber' => $productNumber,
                        'title'         => $title,
                        'metaDesc'      => $desc,
                        'description'   => $a['translated']['description']     ?? $a['description']     ?? '',
                    'keywords'      => $a['translated']['keywords']          ?? $a['keywords']          ?? '',
                    'url'           => isset($seoUrls[$prod['id']]) ? $base . '/' . ltrim($seoUrls[$prod['id']], '/') : '',
                    'langStatus'    => $this->buildLangStatus(
                        $selectedLang, ! empty($title), ! empty($desc),
                        $otherLangStatuses[$prod['id']] ?? [], $meta['languages'], $domainLangIds
                    ),
                ];
            }

            $rows = $this->sortRows($rows, $sort);
        }

        $isGerman     = $this->isGermanLanguage($meta['languages'][$selectedLang] ?? '');
        $customPrompt = $this->customPromptFor($project, $selectedLang, $isGerman);

        return view('seo.products.index', array_merge($meta, compact(
            'project', 'rows', 'selectedSc', 'selectedLang', 'limit', 'search', 'sort', 'isGerman', 'customPrompt'
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
                'sort' => $sort ?? 'name_asc',
                'isGerman' => true,
                'customPrompt' => $this->customPromptFor($project, $selectedLang ?? '', true),
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
                'sort' => $sort ?? 'name_asc',
                'isGerman' => true,
                'customPrompt' => $this->customPromptFor($project, $selectedLang ?? '', true),
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
                'sort' => $sort ?? 'name_asc',
                'isGerman' => true,
                'customPrompt' => $this->customPromptFor($project, $selectedLang ?? '', true),
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
        return $this->guardJson(function () use ($request, $project) {
            $this->bootProject($project);
            $url = $request->input('url', '');

            if (! filter_var($url, FILTER_VALIDATE_URL)) {
                return $this->err('Ungültige URL');
            }

            return $this->ok($this->scraper->scrapeProduct($url));
        });
    }

    // ── AI Generate ───────────────────────────────────────────────────────────

    public function generate(Request $request, SeoProject $project): JsonResponse
    {
        return $this->guardJson(function () use ($request, $project) {
            $this->bootProject($project);

            $v = $request->validate([
                'name'               => 'nullable|string|max:255',
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

            // Shopware-Name kann für nicht übersetzte Produkte leer sein —
            // Produktnummer/H1 dienen dann als Ersatz-Bezeichner für die KI.
            $entityName = $v['name'] ?: ($v['h1'] ?: ($v['productNumber'] ?: 'Produkt'));

            $result = [];
            $tokens = 0;

            if (in_array('title', $v['generate']) || in_array('desc', $v['generate'])) {
                $meta = $this->getAi()->generateMeta(
                    entityName:         $entityName,
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
                $seo = $this->getAi()->generateSeoText(
                    entityName:         $entityName,
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

            $this->log('meta.generated', 'product', '', [], $tokens);

            return $this->ok($result);
        });
    }

    // ── Save to Shopware ──────────────────────────────────────────────────────

    public function save(Request $request, SeoProject $project): JsonResponse
    {
        return $this->guardJson(function () use ($request, $project) {
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
        });
    }
}
