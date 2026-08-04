<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// ─── SeoProject ───────────────────────────────────────────────────────────────

class SeoProject extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'seo_projects';

    protected $fillable = [
        'user_id', 'shopware_credential_id',
        'name', 'shopware_url',
        'sales_channel_id', 'language_id',
        'locale', 'is_active', 'seo_prompt', 'seo_prompts',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'seo_prompts' => 'array',
    ];

    /**
     * Custom AI prompt for a given language, falling back to the legacy
     * project-wide prompt (pre-multilingual) for languages without their own.
     */
    public function promptFor(string $langId): ?string
    {
        return $this->seo_prompts[$langId] ?? $this->seo_prompt ?? null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shopwareCredential(): BelongsTo
    {
        return $this->belongsTo(ApiCredential::class, 'shopware_credential_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(SeoActivityLog::class);
    }
}
