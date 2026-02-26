<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->enum('provider', [
                'shopware',
                'openai',
                'gemini',
                'google_search_console',
            ]);

            $table->string('label')->nullable();       // z.B. "Shop A", "Shop B"

            // Alle Werte AES-256-CBC verschlüsselt via Laravel Crypt
            $table->text('credentials');               // JSON-Blob: { api_key, client_id, ... }

            $table->boolean('is_active')->default(true);
            $table->timestamp('last_tested_at')->nullable();
            $table->boolean('last_test_ok')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'provider', 'label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_credentials');
    }
};
