<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_invoices', function (Blueprint $table) {
            $table->unique('paypal_transaction_id');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('subscription_invoice_id')->nullable()->unique()->constrained('subscription_invoices');
            $table->timestamp('service_period_start')->nullable();
            $table->timestamp('service_period_end')->nullable();
            $table->timestamp('paid_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['subscription_invoice_id']);
            $table->dropUnique(['subscription_invoice_id']);
            $table->dropColumn(['subscription_invoice_id', 'service_period_start', 'service_period_end', 'paid_at']);
        });
        Schema::table('subscription_invoices', fn (Blueprint $table) => $table->dropUnique(['paypal_transaction_id']));
    }
};
