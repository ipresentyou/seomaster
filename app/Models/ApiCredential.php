<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class ApiCredential extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'api_credentials';

    protected $fillable = [
        'user_id',
        'provider',
        'label',
        'credentials',
        'is_active',
        'last_tested_at',
        'last_test_ok',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'last_test_ok'   => 'boolean',
        'last_tested_at' => 'datetime',
    ];

    // ─── Encryption ───────────────────────────────────────────────────────────

    /**
     * Speichert credentials als verschlüsselten JSON-Blob.
     */
    public function setCredentialsAttribute(array|string $value): void
    {
        $json = is_array($value) ? json_encode($value) : $value;
        $this->attributes['credentials'] = Crypt::encryptString($json);
    }

    /**
     * Entschlüsselt und gibt credentials als Array zurück.
     */
    public function getCredentialsAttribute(string $value): array
    {
        try {
            return json_decode(Crypt::decryptString($value), true) ?? [];
        } catch (\Exception) {
            return [];
        }
    }

    /**
     * Liest einen einzelnen Key sicher aus.
     */
    public function getCredential(string $key, mixed $default = null): mixed
    {
        return $this->credentials[$key] ?? $default;
    }

    // ─── Provider-spezifische Builder ─────────────────────────────────────────

    public static function shopwareFor(int $userId, ?string $label = null): ?self
    {
        return static::where('user_id', $userId)
                     ->where('provider', 'shopware')
                     ->when($label, fn($q) => $q->where('label', $label))
                     ->where('is_active', true)
                     ->first();
    }

    public static function openAiFor(int $userId): ?self
    {
        return static::where('user_id', $userId)
                     ->where('provider', 'openai')
                     ->where('is_active', true)
                     ->first();
    }

    public static function gscFor(int $userId): ?self
    {
        return static::where('user_id', $userId)
                     ->where('provider', 'google_search_console')
                     ->where('is_active', true)
                     ->first();
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
