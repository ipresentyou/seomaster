<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * StorefrontScraper
 *
 * Fetches a Shopware storefront page and extracts SEO-relevant fields.
 * Directly ported from the legacy PHP script analyze endpoints.
 */
class StorefrontScraper
{
    /**
     * Scrape a product page.
     * Returns: status, title, metaDesc, h1, content, price, features[]
     */
    public function scrapeProduct(string $url): array
    {
        $result = $this->emptyResult(['price' => '', 'features' => []]);

        [$status, $html] = $this->fetch($url);
        $result['status'] = $status;

        if (! $html) return $result;

        $xpath = $this->buildXpath($html);

        $result['title']   = $this->text($xpath, '//head/title');
        $result['metaDesc'] = $this->attr($xpath, '//head/meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="description"]', 'content');
        $result['h1']      = $this->text($xpath, '//h1');

        // Price
        $priceNode = $xpath->query('//*[contains(@class,"product-detail-price")]')->item(0);
        if ($priceNode) {
            $result['price'] = trim(preg_replace('/\s+/', ' ', $priceNode->textContent));
        } else {
            $result['price'] = $this->attr($xpath, '//meta[@property="product:price:amount"]', 'content');
        }

        // Content
        $result['content'] = $this->collectParagraphs($xpath, [
            '//div[contains(@class,"product-detail-description")]//p',
            '//div[contains(@class,"product-detail")]//p',
        ]);

        // Features
        $featureNodes = $xpath->query(
            '//div[contains(@class,"product-detail-properties")]//dt | //ul[contains(@class,"product-features")]//li'
        );
        foreach ($featureNodes as $node) {
            $text = trim($node->textContent);
            if (strlen($text) > 3 && strlen($text) < 100) {
                $result['features'][] = $text;
                if (count($result['features']) >= 5) break;
            }
        }

        return $result;
    }

    /**
     * Scrape a category page.
     * Returns: status, title, metaDesc, keywords, h1, content
     */
    public function scrapeCategory(string $url): array
    {
        $result = $this->emptyResult(['keywords' => '']);

        [$status, $html] = $this->fetch($url);
        $result['status'] = $status;

        if (! $html) return $result;

        $xpath = $this->buildXpath($html);

        $result['title']    = $this->text($xpath, '//head/title');
        $result['metaDesc'] = $this->attr($xpath, '//head/meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="description"]', 'content');
        $result['keywords'] = $this->attr($xpath, '//head/meta[translate(@name,"ABCDEFGHIJKLMNOPQRSTUVWXYZ","abcdefghijklmnopqrstuvwxyz")="keywords"]', 'content');
        $result['h1']       = $this->text($xpath, '//h1');
        $result['content']  = $this->collectParagraphs($xpath, [
            '//main[contains(@class,"content-main")]//p',
            '//main//p',
        ]);

        return $result;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function fetch(string $url): array
    {
        try {
            $response = Http::withHeaders(['User-Agent' => 'SEOmaster-SeoAudit/1.0'])
                ->timeout(10)
                ->get($url);

            return [$response->status(), $response->successful() ? $response->body() : ''];
        } catch (\Exception) {
            return [0, ''];
        }
    }

    private function buildXpath(string $html): \DOMXPath
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        @$dom->loadHTML($html, LIBXML_NOWARNING | LIBXML_NOERROR);
        return new \DOMXPath($dom);
    }

    private function text(\DOMXPath $xpath, string $query): string
    {
        $node = $xpath->query($query)->item(0);
        return $node ? trim(preg_replace('/\s+/', ' ', $node->textContent)) : '';
    }

    private function attr(\DOMXPath $xpath, string $query, string $attr): string
    {
        $node = $xpath->query($query)->item(0);
        return ($node instanceof \DOMElement) ? trim((string) $node->getAttribute($attr)) : '';
    }

    private function collectParagraphs(\DOMXPath $xpath, array $queries, int $max = 3, int $maxChars = 500): string
    {
        foreach ($queries as $query) {
            $nodes = $xpath->query($query);
            if ($nodes->length > 0) {
                $paragraphs = [];
                foreach ($nodes as $p) {
                    $text = trim(preg_replace('/\s+/', ' ', $p->textContent));
                    if (strlen($text) > 30) {
                        $paragraphs[] = $text;
                        if (count($paragraphs) >= 5) break;
                    }
                }
                if (! empty($paragraphs)) {
                    return mb_substr(implode(' ', array_slice($paragraphs, 0, $max)), 0, $maxChars);
                }
            }
        }
        return '';
    }

    private function emptyResult(array $extra = []): array
    {
        return array_merge(['status' => 0, 'title' => '', 'metaDesc' => '', 'h1' => '', 'content' => ''], $extra);
    }
}
