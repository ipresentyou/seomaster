<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pläne
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // "Starter", "Pro", "Agency"
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 8, 2);
            $table->decimal('price_yearly', 8, 2)->nullable();
            $table->string('paypal_plan_id_monthly')->nullable();
            $table->string('paypal_plan_id_yearly')->nullable();
            $table->json('features')->nullable();       // ["seo_products", "alt_text", "gsc"]
            $table->integer('max_shops')->default(1);
            $table->integer('max_api_calls_per_day')->default(100);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // User-Abonnements
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained();

            $table->string('paypal_subscription_id')->unique()->nullable();
            $table->string('paypal_status')->nullable();   // ACTIVE, SUSPENDED, CANCELLED

            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->enum('status', ['active', 'cancelled', 'suspended', 'trial'])->default('trial');

            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->timestamps();
        });

        // Zahlungshistorie
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->string('paypal_transaction_id')->nullable();
            $table->decimal('amount', 8, 2);
            $table->string('currency', 3)->default('EUR');
            $table->enum('status', ['paid', 'failed', 'refunded'])->default('paid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_invoices');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
