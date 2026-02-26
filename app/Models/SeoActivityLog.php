<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'seo_project_id', 'action',
        'entity_type', 'entity_id',
        'payload', 'ai_tokens_used',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    // ── Relations ─────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(SeoProject::class, 'seo_project_id');
    }

    // ── Static helper ─────────────────────────────────────────────────────────

    public static function record(
        int    $userId,
        int    $projectId,
        string $action,
        string $entityType = '',
        string $entityId   = '',
        array  $payload    = [],
        int    $tokens     = 0,
    ): self {
        return self::create([
            'user_id'        => $userId,
            'seo_project_id' => $projectId,
            'action'         => $action,
            'entity_type'    => $entityType,
            'entity_id'      => $entityId,
            'payload'        => $payload,
            'ai_tokens_used' => $tokens,
        ]);
    }
}
