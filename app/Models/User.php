<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'avatar',
        'timezone',
        'locale',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        // Onboarding
        'onboarding_step',
        'onboarding_completed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'       => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
            'password'                => 'hashed',
            'status'                  => 'string',
            'onboarding_step'         => 'integer',
        ];
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    // ─── Onboarding Helpers ───────────────────────────────────────────────────

    public function hasCompletedOnboarding(): bool
    {
        return $this->onboarding_completed_at !== null;
    }

    public function completeOnboarding(): void
    {
        $this->update([
            'onboarding_completed_at' => now(),
            'onboarding_step'         => 4,
        ]);
    }

    public function advanceOnboardingStep(): void
    {
        $next = min(4, ($this->onboarding_step ?? 1) + 1);
        $this->update(['onboarding_step' => $next]);
    }

    public function onboardingProgress(): int
    {
        if ($this->hasCompletedOnboarding()) return 100;
        return (int) round((($this->onboarding_step - 1) / 3) * 100);
    }

    // ─── Status Helpers ───────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription !== null;
    }

    public function canUseFeature(string $feature): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        $sub = $this->activeSubscription;
        if (! $sub) {
            return false;
        }

        $features = $sub->plan->features ?? [];
        return in_array($feature, $features);
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function apiCredentials(): HasMany
    {
        return $this->hasMany(ApiCredential::class);
    }

    public function credentialsByProvider(string $provider): HasMany
    {
        return $this->apiCredentials()->where('provider', $provider);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
                    ->whereIn('status', ['active', 'trial'])
                    ->latest();
    }

    public function seoProjects(): HasMany
    {
        return $this->hasMany(SeoProject::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(SeoActivityLog::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole("admin");
    }
}
