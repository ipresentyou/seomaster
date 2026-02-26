<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

/**
 * SubscriptionPlanSeeder
 *
 * Legt die 3 Lavarell-Pläne an.
 *
 * SCHRITT 1: Seeder ausführen (Pläne werden ohne PayPal-ID angelegt)
 *   php artisan db:seed --class=SubscriptionPlanSeeder
 *
 * SCHRITT 2: Im PayPal Developer Dashboard Billing Plans erstellen.
 *   https://developer.paypal.com/dashboard/ → Subscriptions → Plans
 *
 * SCHRITT 3: Die generierten Plan-IDs als ENV-Variablen setzen:
 *   PAYPAL_PLAN_STARTER_MONTHLY=P-XXXXXXXXXXXXX
 *   PAYPAL_PLAN_STARTER_YEARLY=P-XXXXXXXXXXXXX
 *   PAYPAL_PLAN_PRO_MONTHLY=P-XXXXXXXXXXXXX
 *   ...
 *
 * SCHRITT 4: Seeder erneut ausführen (aktualisiert die IDs per updateOrCreate)
 *   php artisan db:seed --class=SubscriptionPlanSeeder
 */
class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            // ── Starter ────────────────────────────────────────────────────
            [
                'name'          => 'Starter',
                'slug'          => 'starter',
                'description'   => 'Perfekt für kleine Shops und Einsteiger.',
                'price_monthly' => 19.00,
                'price_yearly'  => 190.00,   // ~2 Monate gratis
                'paypal_plan_id_monthly' => env('PAYPAL_PLAN_STARTER_MONTHLY', ''),
                'paypal_plan_id_yearly'  => env('PAYPAL_PLAN_STARTER_YEARLY', ''),
                'features' => [
                    'seo_products',
                    'seo_categories',
                    'alt_text',
                ],
                'max_shops'            => 1,
                'max_api_calls_per_day' => 100,
                'sort_order'           => 1,
            ],

            // ── Pro ────────────────────────────────────────────────────────
            [
                'name'          => 'Pro',
                'slug'          => 'pro',
                'description'   => 'Für wachsende Shops mit höherem Volumen.',
                'price_monthly' => 49.00,
                'price_yearly'  => 490.00,
                'paypal_plan_id_monthly' => env('PAYPAL_PLAN_PRO_MONTHLY', ''),
                'paypal_plan_id_yearly'  => env('PAYPAL_PLAN_PRO_YEARLY', ''),
                'features' => [
                    'seo_products',
                    'seo_categories',
                    'alt_text',
                    'gsc_integration',
                    'bulk_export',
                ],
                'max_shops'            => 3,
                'max_api_calls_per_day' => 500,
                'sort_order'           => 2,
            ],

            // ── Agency ─────────────────────────────────────────────────────
            [
                'name'          => 'Agency',
                'slug'          => 'agency',
                'description'   => 'Für Agenturen mit mehreren Kundenshops.',
                'price_monthly' => 149.00,
                'price_yearly'  => 1490.00,
                'paypal_plan_id_monthly' => env('PAYPAL_PLAN_AGENCY_MONTHLY', ''),
                'paypal_plan_id_yearly'  => env('PAYPAL_PLAN_AGENCY_YEARLY', ''),
                'features' => [
                    'seo_products',
                    'seo_categories',
                    'alt_text',
                    'gsc_integration',
                    'bulk_export',
                    'csv_import',
                    'white_label',
                    'priority_support',
                ],
                'max_shops'            => 20,
                'max_api_calls_per_day' => 2000,
                'sort_order'           => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        $this->command->info('✅ ' . count($plans) . ' Subscription Plans angelegt/aktualisiert.');
    }
}
