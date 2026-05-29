<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('billing_company')->nullable()->after('name');
            $table->string('billing_name')->nullable()->after('billing_company');
            $table->string('billing_address')->nullable()->after('billing_name');
            $table->string('billing_zip', 20)->nullable()->after('billing_address');
            $table->string('billing_city')->nullable()->after('billing_zip');
            $table->string('billing_country', 2)->default('DE')->after('billing_city');
            $table->string('billing_vat_id', 50)->nullable()->after('billing_country');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'billing_company', 'billing_name', 'billing_address',
                'billing_zip', 'billing_city', 'billing_country', 'billing_vat_id',
            ]);
        });
    }
};
