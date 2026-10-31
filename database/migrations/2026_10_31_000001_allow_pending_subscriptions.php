<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->enum('status', ['active', 'cancelled', 'suspended', 'trial', 'pending'])->default('trial')->change();
        });
    }

    public function down(): void
    {
        DB::table('subscriptions')->where('status', 'pending')->update(['status' => 'cancelled']);
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->enum('status', ['active', 'cancelled', 'suspended', 'trial'])->default('trial')->change();
        });
    }
};
