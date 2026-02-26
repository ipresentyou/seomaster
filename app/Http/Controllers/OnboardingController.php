<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ApiCredential;
use App\Models\SeoProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

/**
 * Onboarding Wizard — 4 steps after first login.
 *
 * Step 1: Welcome       — set name + timezone
 * Step 2: Connect Shop  — add Shopware API credentials (optional but guided)
 * Step 3: First Project — create a SEO project (optional but guided)
 * Step 4: Done          — show plan, links to tools
 */
class OnboardingController extends Controller
{
    // ── Step definitions ──────────────────────────────────────────────────────

    private const STEPS = [
        1 => ['slug' => 'welcome',  'label' => 'Willkommen'],
        2 => ['slug' => 'connect',  'label' => 'Shop verbinden'],
        3 => ['slug' => 'project',  'label' => 'Erstes Projekt'],
        4 => ['slug' => 'done',     'label' => 'Fertig'],
    ];

    // ── Show current step ─────────────────────────────────────────────────────

    public function show(int $step = 1): View|RedirectResponse
    {
        $user = auth()->user();

        // Already done → redirect to dashboard
        if ($user->hasCompletedOnboarding()) {
            return redirect()->route('dashboard');
        }

        // Clamp step to valid range
        $step = max(1, min(4, $step));

        // Don't allow jumping ahead
        if ($step > ($user->onboarding_step ?? 1)) {
            return redirect()->route('onboarding.step', $user->onboarding_step ?? 1);
        }

        $viewData = [
            'step'       => $step,
            'steps'      => self::STEPS,
            'totalSteps' => count(self::STEPS),
            'progress'   => (int) round((($step - 1) / (count(self::STEPS) - 1)) * 100),
            'user'       => $user,
        ];

        return view('onboarding.step-' . $step, $viewData);
    }

    // ── Step 1: Welcome — save name + timezone ────────────────────────────────

    public function saveWelcome(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'min:2', 'max:80'],
            'timezone' => ['required', 'string', 'timezone:all'],
        ]);

        $user = auth()->user();
        $user->update([
            'name'             => $request->name,
            'timezone'         => $request->timezone,
            'onboarding_step'  => 2,
        ]);

        return redirect()->route('onboarding.step', 2);
    }

    // ── Step 2: Connect shop — create Shopware credential ────────────────────

    public function saveConnect(Request $request): RedirectResponse
    {
        if ($request->has('skip')) {
            auth()->user()->update(['onboarding_step' => 3]);
            return redirect()->route('onboarding.step', 3);
        }

        $request->validate([
            'shop_url'      => ['required', 'url', 'max:255'],
            'client_id'     => ['required', 'string', 'max:255'],
            'client_secret' => ['required', 'string', 'max:500'],
        ], [
            'shop_url.required'  => 'Bitte gib die Shop-URL ein.',
            'client_id.required' => 'Bitte gib die Client-ID ein.',
        ]);

        // Optional live test
        $shopUrl   = rtrim($request->shop_url, '/');
        $testPassed = false;
        $testError  = null;

        try {
            $response = Http::timeout(8)->post("{$shopUrl}/api/oauth/token", [
                'grant_type'    => 'client_credentials',
                'client_id'     => $request->client_id,
                'client_secret' => $request->client_secret,
            ]);
            $testPassed = $response->successful() && isset($response->json()['access_token']);
        } catch (\Throwable $e) {
            $testError = 'Verbindung fehlgeschlagen: ' . $e->getMessage();
        }

        if (! $testPassed) {
            return back()
                ->withInput()
                ->withErrors(['shop_url' => $testError ?? 'Verbindung zum Shop fehlgeschlagen. Bitte API-Daten prüfen.']);
        }

        // Determine label
        $host  = parse_url($shopUrl, PHP_URL_HOST) ?: $shopUrl;
        $label = preg_replace('/^www\./', '', $host);

        // Store credential
        ApiCredential::create([
            'user_id'     => auth()->id(),
            'provider'    => 'shopware',
            'label'       => $label,
            'credentials' => [
                'shop_url'      => $shopUrl,
                'client_id'     => $request->client_id,
                'client_secret' => $request->client_secret,
            ],
            'is_active'   => true,
        ]);

        auth()->user()->update(['onboarding_step' => 3]);

        return redirect()->route('onboarding.step', 3)
            ->with('success', "✅ {$label} erfolgreich verbunden!");
    }

    // ── Step 3: First project ─────────────────────────────────────────────────

    public function saveProject(Request $request): RedirectResponse
    {
        if ($request->has('skip')) {
            auth()->user()->completeOnboarding();
            return redirect()->route('onboarding.step', 4);
        }

        $request->validate([
            'name'          => ['required', 'string', 'max:120'],
            'credential_id' => ['required', 'exists:api_credentials,id'],
            'description'   => ['nullable', 'string', 'max:500'],
        ], [
            'name.required'          => 'Bitte gib dem Projekt einen Namen.',
            'credential_id.required' => 'Bitte wähle eine Shop-Verbindung.',
            'credential_id.exists'   => 'Diese Shop-Verbindung wurde nicht gefunden.',
        ]);

        $credential = ApiCredential::where('id', $request->credential_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        SeoProject::create([
            'user_id'                => auth()->id(),
            'shopware_credential_id' => $credential->id,
            'name'                   => $request->name,
            'shopware_url'           => $credential->getCredential('shop_url', ''),
            'is_active'              => true,
        ]);

        auth()->user()->completeOnboarding();

        return redirect()->route('onboarding.step', 4)
            ->with('success', '🎉 Erstes Projekt erstellt!');
    }

    // ── Step 4: Done — skip/complete ─────────────────────────────────────────

    public function complete(): RedirectResponse
    {
        auth()->user()->completeOnboarding();
        return redirect()->route('dashboard')
            ->with('success', '🎉 Willkommen bei SEOmaster! Dein Setup ist abgeschlossen.');
    }

    // ── Skip entire wizard ────────────────────────────────────────────────────

    public function skip(): RedirectResponse
    {
        auth()->user()->completeOnboarding();
        return redirect()->route('dashboard')
            ->with('success', 'Onboarding übersprungen. Du kannst es jederzeit in den Einstellungen nachholen.');
    }

    // ── AJAX: live connection test ─────────────────────────────────────────────

    public function testConnection(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'shop_url'      => ['required', 'url'],
            'client_id'     => ['required', 'string'],
            'client_secret' => ['required', 'string'],
        ]);

        $shopUrl = rtrim($request->shop_url, '/');

        try {
            $response = Http::timeout(8)->post("{$shopUrl}/api/oauth/token", [
                'grant_type'    => 'client_credentials',
                'client_id'     => $request->client_id,
                'client_secret' => $request->client_secret,
            ]);

            if ($response->successful() && isset($response->json()['access_token'])) {
                // Try to get the Shopware version
                $token   = $response->json()['access_token'];
                $infoRes = Http::timeout(5)
                    ->withToken($token)
                    ->get("{$shopUrl}/api/_info/version");

                $version = $infoRes->json()['version'] ?? null;

                return response()->json(['success' => true, 'version' => $version]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Ungültige API-Zugangsdaten. HTTP ' . $response->status(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Verbindung fehlgeschlagen: ' . $e->getMessage(),
            ]);
        }
    }
}
