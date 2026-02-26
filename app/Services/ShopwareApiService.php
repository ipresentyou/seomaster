<?php

namespace App\Services;

use App\Models\ApiCredential;
use App\Models\SeoProject;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

/**
 * ShopwareApiService
 *
 * Laravel port of api_helper.php.
 * Handles OAuth token management (with caching) and all Shopware 6 API calls.
 */
class ShopwareApiService
{
    private string $apiUrl;
    private string $clientId;
    private string $clientSecret;
    private ?string $token = null;
    private string $cacheKey;

    /**
     * Build from a SeoProject (resolves credentials automatically).
     */
    public static function fromProject(SeoProject $project): static
    {
        $credential = ApiCredential::where('user_id', $project->user_id)
            ->where('provider', 'shopware')
            ->first();

        if (! $credential) {
            throw new \RuntimeException('Keine Shopware-Credentials für dieses Projekt gefunden.');
        }

        $creds = $credential->getDecryptedCredentials();

        return new static(
            shopUrl:      rtrim($creds['shop_url'] ?? $project->shopware_url, '/'),
            clientId:     $creds['client_id']     ?? '',
            clientSecret: $creds['client_secret'] ?? '',
        );
    }

    public function __construct(
        string $shopUrl,
        string $clientId,
        string $clientSecret,
    ) {
        $baseUrl        = preg_replace('#/api$#', '', $shopUrl);
        $this->apiUrl   = $baseUrl . '/api';
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->cacheKey = 'sw_token_' . md5($shopUrl . $clientId);
    }

    // ──────────────────────────────────────────────────────────
    // Token Management
    // ──────────────────────────────────────────────────────────

    public function getToken(): string
    {
        if ($this->token) {
            return $this->token;
        }

        // Check cache (tokens are valid for 10 min, we cache for 9)
        $cached = Cache::get($this->cacheKey);
        if ($cached) {
            $this->token = $cached;
            return $this->token;
        }

        $response = Http::asForm()
            ->timeout(15)
            ->withoutVerifying()
            ->post($this->apiUrl . '/oauth/token', [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'Shopware Auth fehlgeschlagen: HTTP ' . $response->status()
                . ' – ' . $response->body()
            );
        }

        $token = $response->json('access_token');
        if (! $token) {
            throw new \RuntimeException('Kein access_token in Shopware-Antwort.');
        }

        Cache::put($this->cacheKey, $token, now()->addMinutes(9));
        $this->token = $token;

        return $this->token;
    }

    /**
     * Test connection – returns ['ok' => bool, 'message' => string]
     */
    public function test(): array
    {
        try {
            $token = $this->getToken();
            $res   = $this->get('/info');
            return ['ok' => $res->successful(), 'message' => 'Verbindung erfolgreich'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    // ──────────────────────────────────────────────────────────
    // Base Request Methods
    // ──────────────────────────────────────────────────────────

    public function get(string $endpoint, array $query = [], ?string $languageId = null): Response
    {
        return Http::withToken($this->getToken())
            ->withHeaders($this->langHeader($languageId))
            ->timeout(20)
            ->withoutVerifying()
            ->get($this->apiUrl . $endpoint, $query);
    }

    public function post(string $endpoint, array $body, ?string $languageId = null): Response
    {
        return Http::withToken($this->getToken())
            ->withHeaders($this->langHeader($languageId))
            ->timeout(20)
            ->withoutVerifying()
            ->post($this->apiUrl . $endpoint, $body);
    }

    public function patch(string $endpoint, array $body, ?string $languageId = null): Response
    {
        return Http::withToken($this->getToken())
            ->withHeaders($this->langHeader($languageId))
            ->timeout(20)
            ->withoutVerifying()
            ->patch($this->apiUrl . $endpoint, $body);
    }

    private function langHeader(?string $languageId): array
    {
        return $languageId ? ['sw-language-id' => $languageId] : [];
    }

    // ──────────────────────────────────────────────────────────
    // Language & Sales Channel
    // ──────────────────────────────────────────────────────────

    public function getLanguages(): array
    {
        $res = $this->post('/search/language', [
            'includes' => ['language' => ['id', 'name']],
        ]);

        $map = [];
        foreach ($res->json('data', []) as $lang) {
            $map[$lang['id']] = $lang['attributes']['name'] ?? $lang['id'];
        }
        return $map;
    }

    public function getSalesChannels(): array
    {
        $res = $this->post('/search/sales-channel', [
            'filter'   => [['type' => 'equals', 'field' => 'typeId', 'value' => '8a243080f92e4c719546314b577cf82b']],
            'includes' => ['sales_channel' => ['id', 'name', 'navigationCategoryId']],
        ]);

        $channels = [];
        foreach ($res->json('data', []) as $sc) {
            $channels[$sc['id']] = $sc['attributes'];
        }
        return $channels;
    }

    public function getDomains(array $salesChannels): array
    {
        $res = $this->post('/search/sales-channel-domain', ['limit' => 200]);

        $domains    = [];
        $domainUrls = [];
        $langMap    = $this->getLanguages();

        foreach ($res->json('data', []) as $d) {
            $a = $d['attributes'];
            if (! isset($salesChannels[$a['salesChannelId']])) continue;

            $scId   = $a['salesChannelId'];
            $langId = $a['languageId'];

            $domains[$scId][$langId]    = $langMap[$langId] ?? $langId;
            $domainUrls[$scId][$langId] = rtrim($a['url'], '/');
        }

        return compact('domains', 'domainUrls', 'langMap');
    }

    // ──────────────────────────────────────────────────────────
    // Products
    // ──────────────────────────────────────────────────────────

    public function getProducts(int $limit = 50, string $languageId = '', string $search = ''): array
    {
        $filters = [
            ['type' => 'equals', 'field' => 'active', 'value' => true],
        ];

        if ($search) {
            $filters[] = ['type' => 'multi', 'operator' => 'or', 'queries' => [
                ['type' => 'contains', 'field' => 'name', 'value' => $search],
                ['type' => 'contains', 'field' => 'productNumber', 'value' => $search],
            ]];
        }

        $res = $this->post('/search/product', [
            'limit'    => $limit,
            'filter'   => $filters,
            'includes' => ['product' => ['id', 'name', 'productNumber', 'translated', 'metaTitle', 'metaDescription', 'description']],
            'sort'     => [['field' => 'name', 'order' => 'ASC']],
        ], $languageId ?: null);

        return $res->json('data', []);
    }

    public function getSeoUrlsForEntities(array $entityIds, string $salesChannelId, string $languageId, string $routeName = 'frontend.detail.page'): array
    {
        if (empty($entityIds)) return [];

        $res = $this->post('/search/seo-url', [
            'filter' => [
                ['type' => 'equals',    'field' => 'isCanonical',    'value' => true],
                ['type' => 'equalsAny', 'field' => 'foreignKey',     'value' => $entityIds],
                ['type' => 'equals',    'field' => 'salesChannelId', 'value' => $salesChannelId],
                ['type' => 'equals',    'field' => 'languageId',     'value' => $languageId],
            ],
        ]);

        $map = [];
        foreach ($res->json('data', []) as $s) {
            $map[$s['attributes']['foreignKey']] = $s['attributes']['seoPathInfo'];
        }
        return $map;
    }

    public function saveProduct(string $productId, array $data, string $languageId): bool
    {
        $res = $this->patch('/product/' . $productId, $data, $languageId);
        return in_array($res->status(), [200, 204]);
    }

    // ──────────────────────────────────────────────────────────
    // Categories
    // ──────────────────────────────────────────────────────────

    public function getCategories(int $limit, string $navRootId, string $languageId): array
    {
        $res = $this->post('/search/category', [
            'limit'  => $limit,
            'filter' => [
                ['type' => 'equals', 'field' => 'active', 'value' => true],
                ['type' => 'multi',  'operator' => 'or',  'queries' => [
                    ['type' => 'equals', 'field' => 'type', 'value' => 'page'],
                    ['type' => 'equals', 'field' => 'type', 'value' => 'folder'],
                    ['type' => 'equals', 'field' => 'type', 'value' => 'landingpage'],
                ]],
                ['type' => 'contains', 'field' => 'path', 'value' => '|' . $navRootId . '|'],
            ],
            'includes' => ['category' => ['id', 'name', 'translated', 'metaTitle', 'metaDescription', 'keywords', 'description', 'type', 'cmsPageId']],
        ], $languageId);

        return $res->json('data', []);
    }

    public function saveCategory(string $categoryId, array $data, string $languageId): bool
    {
        $res = $this->patch('/category/' . $categoryId, $data, $languageId);
        return in_array($res->status(), [200, 204]);
    }

    // ──────────────────────────────────────────────────────────
    // Media / Alt-Text
    // ──────────────────────────────────────────────────────────

    public function getMedia(int $limit, string $languageId, string $filterType = 'missing'): array
    {
        $filters = [];

        if ($filterType === 'missing') {
            $filters[] = ['type' => 'equals', 'field' => 'alt', 'value' => null];
        }

        $payload = [
            'limit'        => $limit,
            'includes'     => ['media' => ['id', 'fileName', 'url', 'alt', 'title', 'fileSize', 'mimeType']],
            'associations' => [
                'productMedia' => [
                    'limit'        => 5,
                    'associations' => [
                        'product' => ['includes' => ['product' => ['id', 'name', 'productNumber']]],
                    ],
                ],
            ],
        ];

        if (! empty($filters)) {
            $payload['filter'] = $filters;
        }

        return $this->post('/search/media', $payload, $languageId)->json() ?? [];
    }

    public function saveMediaAlt(string $mediaId, string $alt, string $languageId): bool
    {
        $res = $this->patch('/media/' . $mediaId, ['alt' => $alt], $languageId);
        return in_array($res->status(), [200, 204]);
    }

    // ──────────────────────────────────────────────────────────
    // Storefront Scraping (for SEO context)
    // ──────────────────────────────────────────────────────────

    public static function scrapeStorefrontPage(string $url): array
    {
        $result = ['status' => 0, 'title' => '', 'metaDesc' => '', 'h1' => '', 'content' => '', 'price' => '', 'features' => [], 'keywords' => ''];

        try {
            $response = Http::withHeaders(['User-Agent' => 'SEOmasterSeoBot/1.0'])
                ->timeout(10)
                ->get($url);

            $result['status'] = $response->status();
            $html = $response->body();

            if (! $html) return $result;

            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            @$dom->loadHTML($html, LIBXML_NOWARNING | LIBXML_NOERROR);
            $xpath = new \DOMXPath($dom);

            $result['title']    = trim($xpath->query('//head/title')->item(0)?->textContent ?? '');
            $result['h1']       = trim(preg_replace('/\s+/', ' ', $xpath->query('//h1')->item(0)?->textContent ?? ''));

            $metaDesc = $xpath->query('//head/meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="description"]')->item(0);
            $result['metaDesc'] = $metaDesc instanceof \DOMElement ? trim($metaDesc->getAttribute('content')) : '';

            $metaKw = $xpath->query('//head/meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="keywords"]')->item(0);
            $result['keywords'] = $metaKw instanceof \DOMElement ? trim($metaKw->getAttribute('content')) : '';

            // Price (product pages)
            $priceNode = $xpath->query('//meta[@property="product:price:amount"]')->item(0);
            $result['price'] = $priceNode instanceof \DOMElement ? $priceNode->getAttribute('content') : '';

            // Body content
            $paragraphs = [];
            foreach ($xpath->query('//main//p') as $p) {
                $text = trim(preg_replace('/\s+/', ' ', $p->textContent));
                if (strlen($text) > 30) {
                    $paragraphs[] = $text;
                    if (count($paragraphs) >= 5) break;
                }
            }
            $result['content'] = mb_substr(implode(' ', array_slice($paragraphs, 0, 3)), 0, 500);

        } catch (\Throwable $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }
}
