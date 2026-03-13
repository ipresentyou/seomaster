<?php

namespace App\Services;

use App\Models\ApiCredential;
use App\Models\SeoProject;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

/**
 * ShopwareService
 *
 * Abstracts all Shopware 6 Admin API communication.
 * Tokens are cached per project to avoid redundant OAuth calls.
 */
class ShopwareService
{
    private string $apiUrl;
    private string $token;
    private SeoProject $project;

    // ── Boot ────────────────────────────────────────────────────────────────

    public function __construct(SeoProject $project, ApiCredential $credential)
    {
        $this->project = $project;

        $shopUrl          = rtrim($credential->getCredential('shop_url', ''), '/');
        $this->apiUrl     = $shopUrl . '/api';
        $this->token      = $this->resolveToken($shopUrl, $credential);
    }

    /**
     * Convenience factory: resolve credential for this project + user.
     */
    public static function forProject(SeoProject $project): self
    {
        $credential = ApiCredential::shopwareFor($project->user_id);

        if (! $credential) {
            throw new \RuntimeException('Keine Shopware-Credentials für dieses Projekt gefunden.');
        }

        return new self($project, $credential);
    }

    // ── Token ────────────────────────────────────────────────────────────────

    private function resolveToken(string $shopUrl, ApiCredential $credential): string
    {
        $cacheKey = 'sw_token_' . $this->project->id;

        return Cache::remember($cacheKey, 1800, function () use ($shopUrl, $credential) {
            $response = Http::asForm()->post($shopUrl . '/api/oauth/token', [
                'grant_type'    => 'client_credentials',
                'client_id'     => $credential->getCredential('client_id'),
                'client_secret' => $credential->getCredential('client_secret'),
            ]);

            if (! $response->successful()) {
                throw new \RuntimeException(
                    'Shopware OAuth-Fehler: HTTP ' . $response->status() . ' – ' . $response->body()
                );
            }

            return $response->json('access_token');
        });
    }

    // ── Language / SalesChannel ───────────────────────────────────────────────

    public function getLanguages(): array
    {
        $res = $this->post('/search/language', [
            'includes' => ['language' => ['id', 'name']],
        ]);

        $map = [];
        foreach ($res->json('data', []) as $l) {
            $map[$l['id']] = $l['name'] ?? '';
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

            if (empty($sc['id'])) continue; $id = $sc['id']; $channels[$id] = $sc;
        }
        return $channels;
    }

    public function getDomains(): array
    {
        $res = $this->post('/search/sales-channel-domain', [
            'limit' => 200,
        ]);

        $domains = [];
        foreach ($res->json('data', []) as $d) {
            $a = $d;
            $domains[$a['salesChannelId']][$a['languageId']] = [
                'url'      => rtrim($a['url'], '/'),
                'langId'   => $a['languageId'],
            ];
        }
        return $domains;
    }

    // ── Products ──────────────────────────────────────────────────────────────

    public function getProducts(string $langId, int $limit = 50, string $search = ''): array
    {
        $filters = [
            ['type' => 'equals', 'field' => 'active', 'value' => true],
        ];

        if ($search) {
            $filters[] = ['type' => 'multi', 'operator' => 'or', 'queries' => [
                ['type' => 'contains', 'field' => 'name',          'value' => $search],
                ['type' => 'contains', 'field' => 'productNumber', 'value' => $search],
            ]];
        }

        $res = $this->post('/search/product', [
            'limit'    => $limit,
            'filter'   => $filters,
            'includes' => ['product' => ['id', 'name', 'productNumber', 'translated', 'metaTitle', 'metaDescription', 'keywords', 'description']],
            'sort'     => [['field' => 'name', 'order' => 'ASC']],
        ], $langId);

        return $res->json('data', []);
    }

    public function saveProduct(string $productId, string $langId, array $payload): bool
    {
        $res = $this->patch('/product/' . $productId, $payload, $langId);
        $ok = in_array($res->status(), [200, 204]);
        if ($ok) $this->clearHttpCache();
        
        return $ok;
    }

    // ── Categories ────────────────────────────────────────────────────────────

    public function getCategories(string $langId, string $navRootId, int $limit = 50): array
    {
        $res = $this->post('/search/category', [
            'limit'  => $limit,
            'filter' => [
                ['type' => 'equals', 'field' => 'active', 'value' => true],
                ['type' => 'multi', 'operator' => 'or', 'queries' => [
                    ['type' => 'equals', 'field' => 'type', 'value' => 'page'],
                    ['type' => 'equals', 'field' => 'type', 'value' => 'folder'],
                    ['type' => 'equals', 'field' => 'type', 'value' => 'landingpage'],
                ]],
                ['type' => 'multi', 'operator' => 'or', 'queries' => [
                    ['type' => 'contains', 'field' => 'path', 'value' => '|' . $navRootId . '|'],
                    ['type' => 'equals',   'field' => 'id',   'value' => $navRootId],
                ]],
            ],
            'includes' => ['category' => ['id', 'name', 'translated', 'metaTitle', 'metaDescription', 'keywords', 'description', 'type', 'cmsPageId']],
        ], $langId);

        return $res->json('data', []);
    }

    public function saveCategory(string $catId, string $langId, array $payload): bool
    {
        $res = $this->patch('/category/' . $catId, $payload, $langId);
        $ok = in_array($res->status(), [200, 204]);
        if ($ok) $this->clearHttpCache();
        return $ok;
    }

    // ── Media (Alt-Text) ──────────────────────────────────────────────────────

    public function getMedia(string $langId, int $limit = 50, bool $missingOnly = true): array
    {
        $payload = [
            'limit'    => $limit,
            'includes' => ['media' => ['id', 'fileName', 'url', 'alt', 'title', 'fileSize', 'mimeType']],
            'associations' => [
                'productMedia' => [
                    'limit'        => 10,
                    'associations' => [
                        'product' => ['includes' => ['product' => ['id', 'name', 'productNumber']]],
                    ],
                ],
            ],
        ];

        if ($missingOnly) {
            $payload['filter'] = [['type' => 'equals', 'field' => 'alt', 'value' => null]];
        }

        $res = $this->post('/search/media', $payload, $langId);
        return $res->json() ?? [];
    }

    public function saveMediaAlt(string $mediaId, string $langId, string $alt): bool
    {
        $res = $this->patch('/media/' . $mediaId, ['alt' => strip_tags($alt)], $langId);
        $ok = in_array($res->status(), [200, 204]);
        if ($ok) $this->clearHttpCache();
        
        return $ok;
    }

    // ── SEO URLs ──────────────────────────────────────────────────────────────

    public function getSeoUrls(array $foreignKeys, string $salesChannelId, string $langId): array
    {
        $res = $this->post('/search/seo-url', [
            'filter' => [
                ['type' => 'equals',    'field' => 'isCanonical',   'value' => true],
                ['type' => 'equalsAny', 'field' => 'foreignKey',    'value' => $foreignKeys],
                ['type' => 'equals',    'field' => 'salesChannelId','value' => $salesChannelId],
                ['type' => 'equals',    'field' => 'languageId',    'value' => $langId],
            ],
        ]);

        $urls = [];
        foreach ($res->json('data', []) as $s) {
            $urls[$s['foreignKey']] = $s['seoPathInfo'];
        }
        return $urls;
    }

    // ── HTTP Helpers ──────────────────────────────────────────────────────────

    private function headers(string $langId = ''): array
    {
        $h = [
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
        if ($langId) {
            $h['sw-language-id'] = $langId;
        }
        return $h;
    }

    private function post(string $path, array $body, string $langId = ''): Response
    {
        return Http::withHeaders($this->headers($langId))
            ->timeout(60) // Increased from 20 to 60 seconds
            ->retry(2, 1000) // Retry 2 times with 1s delay
            ->post($this->apiUrl . $path, $body);
    }

    private function patch(string $path, array $body, string $langId = ''): Response
    {
        return Http::withHeaders($this->headers($langId))
            ->timeout(60) // Increased from 20 to 60 seconds
            ->retry(2, 1000) // Retry 2 times with 1s delay
            ->patch($this->apiUrl . $path, $body);
    }

    private function get(string $path, string $langId = ''): Response
    {
        return Http::withHeaders($this->headers($langId))
            ->timeout(60) // Increased from 20 to 60 seconds
            ->retry(2, 1000) // Retry 2 times with 1s delay
            ->get($this->apiUrl . $path);
    }


    public function clearHttpCache(): void
    {
        try {
            $this->post('/_action/cache', []);
        } catch (\Throwable) {}
    }
}
