<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Subscription;

class SubscriptionPlan extends Model
{
    protected $table = 'subscription_plans';
    
    protected $fillable = [
        'name', 'slug', 'description',
        'price_monthly', 'price_yearly',
        'features',
        'paypal_plan_id_monthly', 'paypal_plan_id_yearly',
        'is_active', 'max_shops', 'max_api_calls_per_day', 'trial_days',
    ];

    protected $casts = [
        'features'       => 'array',
        'is_active'      => 'boolean',
        'price_monthly'  => 'float',
        'price_yearly'   => 'float',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'subscription_plan_id');
    }
}
