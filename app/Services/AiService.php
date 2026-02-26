<?php

namespace App\Services;

use App\Models\ApiCredential;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AiService
 *
 * Abstracts OpenAI and Gemini for:
 * - Alt-text generation (Vision via GPT-4o)
 * - Meta title + description generation
 * - Full SEO text / category description generation
 */
class AiService
{
    private string $apiKey;
    private string $apiUrl;
    private string $model;
    private string $visionModel;

    public function __construct(
        string $apiKey,
        string $apiUrl,
        string $model = 'gpt-4o-mini',
        string $visionModel = 'gpt-4o'
    ) {
        $this->apiKey      = $apiKey;
        $this->apiUrl      = rtrim($apiUrl, '/');
        $this->model       = $model;
        $this->visionModel = $visionModel;
    }

    /**
     * Build from user credentials (prefers OpenAI, falls back to Gemini).
     */
    /** Alias used by BaseSeoController */
    public static function forUser(int $userId): static
    {
        return static::fromUser($userId);
    }

    public static function fromUser(int $userId): static
    {
        $cred = ApiCredential::where('user_id', $userId)
            ->whereIn('provider', ['openai', 'gemini'])
            ->orderByRaw("CASE provider WHEN 'openai' THEN 0 ELSE 1 END")
            ->first();

        if (! $cred) {
            throw new \RuntimeException('Keine KI-Credentials gefunden. Bitte OpenAI oder Gemini verbinden.');
        }

        $decrypted = $cred->credentials;
        $apiKey    = $decrypted['api_key'] ?? '';

        if ($cred->provider === 'gemini') {
            return new static(
                apiKey:      $apiKey,
                apiUrl:      'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions',
                model:       'gemini-1.5-flash',
                visionModel: 'gemini-1.5-pro',
            );
        }

        return new static(
            apiKey:      $apiKey,
            apiUrl:      'https://api.openai.com/v1/chat/completions',
            model:       'gpt-4o-mini',
            visionModel: 'gpt-4o',
        );
    }

    // ──────────────────────────────────────────────────────────
    // Alt-Text  (Vision)
    // ──────────────────────────────────────────────────────────

    /**
     * @return array{altText: string}|array{error: string}
     */
    public function generateAltText(
        string $imageUrl,
        string $fileName,
        string $targetLanguage = '',
        string $productContext = '',
        string $domainName = '',
        string $customInstructions = ''
    ): array {
        $system = "You are an SEO expert specializing in accessible, SEO-optimized image alt text.
Analyze the image and create a descriptive alt text that:
- Describes what's visible in the image
- Is concise (50-125 characters ideal)
- Includes relevant keywords naturally
- Is accessible for screen readers
- Avoids phrases like 'image of' or 'picture of'
Respond ONLY in JSON format: {\"altText\":\"...\"}";

        if ($targetLanguage || $productContext || $domainName || $customInstructions) {
            $system .= "\n\nCONTEXT:";
            if ($domainName)         $system .= "\n- Domain: {$domainName}";
            if ($targetLanguage)     $system .= "\n- Language: {$targetLanguage}";
            if ($productContext)     $system .= "\n- Product/Context: {$productContext}";
            if ($customInstructions) $system .= "\n- Requirements:\n" . strip_tags($customInstructions);
        }

        $userText  = "Image filename: '{$fileName}'\n";
        if ($productContext) $userText .= "Product context: {$productContext}\n";
        $userText .= "\nCreate an SEO-optimized alt text for this image.";

        $payload = [
            'model'           => $this->visionModel,
            'response_format' => ['type' => 'json_object'],
            'max_tokens'      => 300,
            'messages'        => [[
                'role'    => 'user',
                'content' => [
                    ['type' => 'text',      'text'      => $system . "\n\n" . $userText],
                    ['type' => 'image_url', 'image_url' => ['url' => $imageUrl, 'detail' => 'low']],
                ],
            ]],
        ];

        return $this->call($payload);
    }

    // ──────────────────────────────────────────────────────────
    // Meta Data  (Title + Description [+ Keywords])
    // ──────────────────────────────────────────────────────────

    /**
     * @return array{title: string, metaDesc: string, keywords?: string}|array{error: string}
     */
    public function generateMeta(
        string $entityName,
        string $entityType = 'product',
        string $pageContent = '',
        string $h1 = '',
        string $targetLanguage = '',
        string $storefrontDomain = '',
        string $customInstructions = '',
        string $existingKeywords = '',
        array  $extra = []
    ): array {
        $includeKeywords = ($entityType === 'category');
        $jsonSchema      = $includeKeywords
            ? '{"title":"...","metaDesc":"...","keywords":"..."}'
            : '{"title":"...","metaDesc":"..."}';

        $system  = "You are an e-commerce SEO expert.\n";
        $system .= "Respond ONLY in JSON: {$jsonSchema}\n";
        $system .= "- Title: Max 60 characters, keyword-optimized, compelling\n";
        $system .= "- Meta Description: Max 155 characters, call-to-action, relevant to content\n";
        if ($includeKeywords) {
            $system .= "- Keywords: 5-10 relevant keywords, comma-separated\n";
        }
        $system .= "CRITICAL: Generate in the SAME LANGUAGE as the page content provided!";

        if ($existingKeywords) {
            $system .= "\n- Existing Keywords to improve: {$existingKeywords}";
        }

        if ($storefrontDomain || $targetLanguage || $customInstructions) {
            $system .= "\n\nADDITIONAL CONTEXT:";
            if ($storefrontDomain)   $system .= "\n- Domain: {$storefrontDomain}";
            if ($targetLanguage)     $system .= "\n- Language: {$targetLanguage}";
            if ($customInstructions) $system .= "\n\nBrand Requirements:\n" . strip_tags($customInstructions);
        }

        $user = "Name: '{$entityName}'\n";
        foreach ($extra as $k => $v) {
            if ($v) $user .= ucfirst($k) . ": {$v}\n";
        }
        if ($h1)          $user .= "H1: '{$h1}'\n";
        if ($pageContent) $user .= "Content:\n{$pageContent}\n\n";
        $user .= "Create optimized SEO meta data. Use SAME LANGUAGE as content.";

        $payload = [
            'model'           => $this->model,
            'response_format' => ['type' => 'json_object'],
            'temperature'     => 0.7,
            'messages'        => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user',   'content' => $user],
            ],
        ];

        return $this->call($payload);
    }

    // ──────────────────────────────────────────────────────────
    // SEO Text  (Full HTML description)
    // ──────────────────────────────────────────────────────────

    /**
     * @return array{seoText: string}|array{error: string}
     */
    public function generateSeoText(
        string $entityName,
        string $entityType = 'product',
        string $pageContent = '',
        string $h1 = '',
        string $targetLanguage = '',
        string $customInstructions = '',
        array  $extra = []
    ): array {
        [$minWords, $maxWords] = ($entityType === 'product') ? [200, 400] : [300, 600];

        $system  = $entityType === 'product'
            ? "You are an e-commerce copywriter. Create a compelling product description optimized for SEO."
            : "You are an SEO content expert. Create a comprehensive category description optimized for SEO.";

        $htmlTags = $entityType === 'product'
            ? "<h2>, <h3>, <p>, <ul>, <li>, <strong>"
            : "<h2>, <h3>, <p>, <ul>, <li>";

        $system .= "\nRespond in JSON: {\"seoText\":\"...\"}
- Length: {$minWords}-{$maxWords} words
- Structure: HTML with {$htmlTags}
- Keywords: Naturally integrated
- CRITICAL: Generate in SAME LANGUAGE as page content!";

        if ($targetLanguage || $customInstructions) {
            $system .= "\n\nCONTEXT:";
            if ($targetLanguage)     $system .= "\n- Language: {$targetLanguage}";
            if ($customInstructions) $system .= "\n- Requirements:\n" . strip_tags($customInstructions);
        }

        $user = "Name: '{$entityName}'\n";
        foreach ($extra as $k => $v) {
            if ($v) $user .= ucfirst($k) . ": {$v}\n";
        }
        if ($h1)          $user .= "H1: '{$h1}'\n";
        if ($pageContent) $user .= "Content:\n{$pageContent}\n\n";
        $user .= "Create SEO-optimized description with HTML formatting.";

        $payload = [
            'model'           => $this->model,
            'response_format' => ['type' => 'json_object'],
            'temperature'     => 0.7,
            'messages'        => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user',   'content' => $user],
            ],
        ];

        return $this->call($payload);
    }

    // ──────────────────────────────────────────────────────────
    // Internal
    // ──────────────────────────────────────────────────────────

    private function call(array $payload): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(45)
                ->post($this->apiUrl, $payload);

            if (! $response->successful()) {
                $error = $response->json('error.message') ?? $response->body();
                Log::warning('AiService error', ['status' => $response->status(), 'error' => $error]);
                return ['error' => $error];
            }

            $content = $response->json('choices.0.message.content');
            $decoded = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['error' => 'Invalid JSON from AI: ' . $content];
            }

            return $decoded;

        } catch (\Throwable $e) {
            Log::error('AiService exception', ['message' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }
}
