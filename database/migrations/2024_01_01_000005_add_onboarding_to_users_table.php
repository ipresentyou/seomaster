<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Current step the user is on (1–4), null = not started
            $table->unsignedTinyInteger('onboarding_step')
                ->default(1)
                ->after('locale');

            // Set when the user completes or skips the wizard
            $table->timestamp('onboarding_completed_at')
                ->nullable()
                ->after('onboarding_step');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['onboarding_step', 'onboarding_completed_at']);
        });
    }
};
