<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiUsage extends Model
{
    protected $table = 'api_usages';
    
    protected $fillable = [
        'user_id',
        'service',
        'feature',
        'usage_date',
        'calls_count',
    ];

    protected $casts = [
        'usage_date' => 'date',
        'calls_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Track API usage and check daily limit
     */
    public static function trackAndCheckLimit(int $userId, string $service, string $feature, int $dailyLimit = 10): bool
    {
        $today = now()->toDateString();
        
        $usage = static::firstOrCreate(
            [
                'user_id' => $userId,
                'service' => $service,
                'usage_date' => $today,
            ],
            [
                'feature' => $feature,
                'calls_count' => 0,
            ]
        );

        // Check if limit exceeded
        if ($usage->calls_count >= $dailyLimit) {
            return false; // Limit reached
        }

        // Increment usage
        $usage->increment('calls_count');
        return true; // Allowed
    }

    /**
     * Get today's usage for user
     */
    public static function getTodayUsage(int $userId, string $service): int
    {
        return static::where('user_id', $userId)
            ->where('service', $service)
            ->where('usage_date', now()->toDateString())
            ->sum('calls_count');
    }
}
