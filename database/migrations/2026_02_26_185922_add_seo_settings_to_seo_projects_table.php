<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_projects', function (Blueprint $table) {
            $table->text('seo_prompt')->nullable()->after('locale');
        });
    }

    public function down(): void
    {
        Schema::table('seo_projects', function (Blueprint $table) {
            $table->dropColumn('seo_prompt');
        });
    }
};
