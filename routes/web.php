<?php

use App\Http\Controllers\ApiCredentialController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SeoProjectController;
use App\Http\Controllers\Seo\AltTextController;
use App\Http\Controllers\Seo\CategorySeoController;
use App\Http\Controllers\Seo\ProductSeoController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

// ─── Public ───────────────────────────────────────────────────────────────────

Route::get('/', function() { return auth()->check() ? redirect()->route('dashboard') : view('landing'); });

// Auth Routes
require __DIR__ . '/auth.php';

// ─── Onboarding (auth + verified, but NOT onboarding middleware) ───────────────

Route::middleware(['auth', 'verified', 'user.active'])
    ->prefix('onboarding')
    ->name('onboarding.')
    ->group(function () {

        // Show step  (GET /onboarding/1 … /onboarding/4)
        Route::get('/{step}', [OnboardingController::class, 'show'])
            ->where('step', '[1-4]')
            ->name('step');

        // Redirect /onboarding → current step
        Route::get('/', fn() => redirect()->route('onboarding.step', auth()->user()->onboarding_step ?? 1))
            ->name('index');

        // Step saves
        Route::post('/welcome',  [OnboardingController::class, 'saveWelcome'])->name('welcome.save');
        Route::post('/connect',  [OnboardingController::class, 'saveConnect'])->name('connect.save');
        Route::post('/project',  [OnboardingController::class, 'saveProject'])->name('project.save');
        Route::post('/complete', [OnboardingController::class, 'complete'])->name('complete');
        Route::get('/skip',      [OnboardingController::class, 'skip'])->name('skip');

        // Live connection test (AJAX from step 2)
        Route::post('/test-connection', [OnboardingController::class, 'testConnection'])
            ->name('connect.test');
    });

// ─── Authenticated ─────────────────────────────────────────────────────────────

Route::middleware(['auth', 'verified', 'user.active'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil
    Route::get('/profile',        [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',      [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/profile',     [ProfileController::class, 'destroy'])->name('profile.destroy');

    // API Credentials
    Route::prefix('credentials')->name('credentials.')->group(function () {
        Route::get('/',                   [ApiCredentialController::class, 'index'])->name('index');
        Route::get('/create',             [ApiCredentialController::class, 'create'])->name('create');
        Route::post('/',                  [ApiCredentialController::class, 'store'])->name('store');
        Route::delete('/{credential}',    [ApiCredentialController::class, 'destroy'])->name('destroy');
        Route::post('/{credential}/test', [ApiCredentialController::class, 'test'])->name('test');
    });

    // SEO Projekte
    Route::resource('projects', SeoProjectController::class);

    // SEO Tools (nur mit aktivem Abo oder Trial)
    Route::middleware('subscription.active')
        ->prefix('projects/{project}/seo')
        ->name('seo.')
        ->group(function () {

            // ── Produkte ──────────────────────────────────────────────────────
            Route::get('/products',             [ProductSeoController::class, 'index'])->name('products');
            Route::get('/products/analyze',     [ProductSeoController::class, 'analyze'])->name('products.analyze');
            Route::post('/products/generate',   [ProductSeoController::class, 'generate'])->name('products.generate');
            Route::post('/products/save',       [ProductSeoController::class, 'save'])->name('products.save');

            // ── Kategorien ────────────────────────────────────────────────────
            Route::get('/categories',           [CategorySeoController::class, 'index'])->name('categories');
            Route::get('/categories/analyze',   [CategorySeoController::class, 'analyze'])->name('categories.analyze');
            Route::post('/categories/generate', [CategorySeoController::class, 'generate'])->name('categories.generate');
            Route::post('/categories/save',     [CategorySeoController::class, 'save'])->name('categories.save');
            Route::post('/categories/prompt',   [CategorySeoController::class, 'savePrompt'])->name('categories.prompt');
            Route::post('/prompt',               [CategorySeoController::class, 'savePrompt'])->name('prompt');

            // ── Bild Alt-Texte ─────────────────────────────────────────────────
            Route::get('/alt-text',             [AltTextController::class, 'index'])->name('alttext');
            Route::post('/alt-text/generate',   [AltTextController::class, 'generate'])->name('alttext.generate');
            Route::post('/alt-text/save',       [AltTextController::class, 'save'])->name('alttext.save');
            Route::post('/alt-text/batch',      [AltTextController::class, 'batchSave'])->name('alttext.batch');
        });

    // Abonnements
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/',             [SubscriptionController::class, 'index'])->name('index');
        Route::post('/checkout',    [SubscriptionController::class, 'checkout'])->name('checkout');
        Route::get('/success',      [SubscriptionController::class, 'success'])->name('success');
        Route::get('/cancel',       [SubscriptionController::class, 'cancel'])->name('cancel');
        Route::post('/cancel-plan', [SubscriptionController::class, 'cancelPlan'])->name('cancel-plan');
    });
});

// ── PayPal Webhook (kein Auth nötig) ─────────────────────────────────────────

Route::post('/webhooks/paypal', [SubscriptionController::class, 'webhook'])
    ->name('webhooks.paypal')
    ->withoutMiddleware(['web']);

Route::view('/terms', 'legal.terms')->name('terms');
Route::view('/privacy', 'legal.privacy')->name('privacy');

Route::get('/favicon.ico', function() {
    return response()->file(public_path('favicon.svg'), ['Content-Type' => 'image/svg+xml']);
});
