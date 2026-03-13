<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $table = 'subscriptions';
    
    protected $fillable = [
        'user_id', 'subscription_plan_id',
        'status', 'billing_cycle',
        'trial_ends_at', 'current_period_start', 'current_period_end',
        'cancelled_at', 'paypal_subscription_id', 'paypal_order_id',
        'amount', 'currency',
    ];

    protected $casts = [
        'trial_ends_at'         => 'datetime',
        'current_period_start'  => 'datetime',
        'current_period_end'    => 'datetime',
        'cancelled_at'          => 'datetime',
        'amount'                => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
    public function invoices()
    {
        return $this->hasMany(self::class, "id", "id")->whereRaw("1=0");
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trial']);
    }

    public function isOnTrial(): bool
    {
        return $this->status === 'trial' && $this->trial_ends_at?->isFuture();
    }

    public function isTrial(): bool
    {
        return $this->status === 'trial';
    }
}
