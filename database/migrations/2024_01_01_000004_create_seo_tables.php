<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Shopware-Shop-Konfigurationen pro User
        Schema::create('seo_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shopware_credential_id')
                  ->nullable()
                  ->constrained('api_credentials')
                  ->nullOnDelete();

            $table->string('name');                    // "Shop DE"
            $table->string('shopware_url');            // https://shop.example.com
            $table->string('sales_channel_id')->nullable();
            $table->string('language_id')->nullable();
            $table->string('locale')->default('de-DE');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Audit-Log für alle SEO-Aktionen
        Schema::create('seo_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seo_project_id')->nullable()->constrained()->nullOnDelete();

            $table->string('action');                  // "alt_text.generated", "meta.saved"
            $table->string('entity_type')->nullable(); // "product", "category", "media"
            $table->string('entity_id')->nullable();
            $table->json('payload')->nullable();       // { before: {}, after: {} }
            $table->integer('ai_tokens_used')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_activity_logs');
        Schema::dropIfExists('seo_projects');
    }
};
