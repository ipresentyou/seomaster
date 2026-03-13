<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('api_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('service'); // openai, gemini
            $table->string('feature'); // alt_text, meta_generation, etc.
            $table->date('usage_date'); // YYYY-MM-DD for daily grouping
            $table->integer('calls_count')->default(0);
            $table->timestamps();
            
            $table->unique(['user_id', 'service', 'usage_date'], 'unique_daily_usage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_usages');
    }
};
