<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionInvoice extends Model
{
    protected $fillable = ['subscription_id', 'paypal_transaction_id', 'amount', 'currency', 'status', 'paid_at'];

    protected $casts = ['amount' => 'float', 'paid_at' => 'datetime'];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
