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

    protected $fillable = [
        'user_id', 'shopware_credential_id',
        'name', 'shopware_url',
        'sales_channel_id', 'language_id',
        'locale', 'is_active', 'seo_prompt',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

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
