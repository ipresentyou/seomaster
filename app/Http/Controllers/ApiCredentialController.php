<?php

namespace App\Http\Controllers;

use App\Models\ApiCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApiCredentialController extends Controller
{
    public function index()
    {
        $credentials = auth()->user()->apiCredentials()->get()->groupBy('provider');
        return view('credentials.index', compact('credentials'));
    }

    public function create(Request $request)
    {
        $preselected = $request->get('provider', 'shopware'); // Default to shopware
        \Log::info('API Credential Create:', ['preselected' => $preselected, 'request_provider' => $request->get('provider')]);
        return view('credentials.create', compact('preselected'));
    }

    public function store(Request $request)
    {
        // Debug: Log all incoming data
        \Log::info('API Credential Store Request:', [
            'all_data' => $request->all(),
            'credentials' => $request->input('credentials'),
            'provider' => $request->input('provider')
        ]);

        $data = $request->validate([
            'provider' => ['required', 'in:shopware,openai,gemini,google_search_console'],
            'label'    => ['nullable', 'string', 'max:100'],
            'credentials' => ['required', 'array'],
        ]);

        // Provider-spezifische Validierung
        $validationResult = $this->validateProviderCredentials($data['provider'], $data['credentials']);
        if ($validationResult !== true) {
            return $validationResult; // Return the redirect with errors
        }

        try {
            auth()->user()->apiCredentials()->updateOrCreate(
                ['provider' => $data['provider'], 'label' => $data['label']],
                [
                'provider'    => $data['provider'],
                'label'       => $data['label'],
                'credentials' => $data['credentials'],
            ]);

            return redirect()->route('credentials.index')
                ->with('success', '✅ API-Credentials gespeichert.');

        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return back()
                ->withInput()
                ->withErrors(['label' => '⚠️ Eine API-Credential mit diesem Label und Provider existiert bereits. Bitte wähle ein anderes Label.']);
        } catch (\Exception $e) {
            \Log::error('API Credential Store Error:', ['error' => $e->getMessage()]);
            return back()
                ->withInput()
                ->withErrors(['general' => '⚠️ Ein Fehler ist aufgetreten. Bitte versuche es erneut.']);
        }
    }

    public function destroy(ApiCredential $credential)
    {
        
        $credential->delete();
        return back()->with('success', '🗑️ Credentials gelöscht.');
    }

    public function test(ApiCredential $credential)
    {
        

        $ok = match($credential->provider) {
            'shopware'               => $this->testShopware($credential),
            'openai'                 => $this->testOpenAi($credential),
            'gemini'                 => $this->testGemini($credential),
            'google_search_console'  => $this->testGsc($credential),
            default                  => false,
        };

        $credential->update([
            'last_tested_at' => now(),
            'last_test_ok'   => $ok,
        ]);

        return response()->json(['ok' => $ok]);
    }

    // ─── Provider Tests ───────────────────────────────────────────────────────

    private function testShopware(ApiCredential $cred): bool
    {
        try {
            $creds = $cred->credentials;
            $shopUrl = rtrim($creds['shop_url'], '/'); // Remove trailing slash
            $url = $shopUrl . '/api/oauth/token';
            
            \Log::info('Shopware API Test:', [
                'url' => $url,
                'client_id' => $creds['client_id'],
                'has_client_secret' => !empty($creds['client_secret'])
            ]);
            
            $res = Http::timeout(5)->post($url, [
                'grant_type'    => 'client_credentials',
                'client_id'     => $creds['client_id'],
                'client_secret' => $creds['client_secret'],
            ]);
            
            \Log::info('Shopware API Response:', [
                'status' => $res->status(),
                'successful' => $res->successful(),
                'body' => $res->body(),
                'json' => $res->json()
            ]);
            
            return $res->successful() && isset($res->json()['access_token']);
        } catch (\Throwable $e) {
            \Log::error('Shopware API Test Exception:', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function testOpenAi(ApiCredential $cred): bool
    {
        try {
            $res = Http::withToken($cred->getCredential('api_key'))
                ->timeout(5)
                ->get('https://api.openai.com/v1/models');
            return $res->successful();
        } catch (\Throwable) {
            return false;
        }
    }

    private function testGemini(ApiCredential $cred): bool
    {
        try {
            $key = $cred->getCredential('api_key');
            $res = Http::timeout(5)->get(
                "https://generativelanguage.googleapis.com/v1beta/models?key={$key}"
            );
            return $res->successful();
        } catch (\Throwable) {
            return false;
        }
    }

    private function testGsc(ApiCredential $cred): bool
    {
        // GSC OAuth2 – vereinfacht: prüfe ob refresh_token vorhanden
        return ! empty($cred->getCredential('refresh_token'));
    }

    // ─── Validierung ──────────────────────────────────────────────────────────

    private function validateProviderCredentials(string $provider, array $creds): mixed
    {
        $required = match($provider) {
            'shopware'               => ['shop_url', 'client_id', 'client_secret'],
            'openai'                 => ['api_key'],
            'gemini'                 => ['api_key'],
            'google_search_console'  => ['gsc_client_id', 'gsc_client_secret', 'gsc_refresh_token'],
        };

        $errs = [];
        foreach ($required as $field) {
            if (!isset($creds[$field]) || $creds[$field] === '' || trim($creds[$field]) === '') {
                $errs["credentials.{$field}"] = "Bitte {$field} ausfuellen.";
            }
        }
        if (!empty($errs)) {
            return back()->withInput()->withErrors($errs);
        }
        
        return true; // Success
    }
}
