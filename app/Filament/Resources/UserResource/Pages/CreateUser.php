<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * After creating the user, optionally start a trial subscription.
     */
    protected function afterCreate(): void
    {
        $user = $this->record;

        // Auto-start trial on the Pro plan
        $proPlan = SubscriptionPlan::where('slug', 'pro')->first();

        if ($proPlan) {
            Subscription::create([
                'user_id'              => $user->id,
                'subscription_plan_id' => $proPlan->id,
                'billing_cycle'        => 'monthly',
                'status'               => 'trial',
                'trial_ends_at'        => now()->addDays(14),
            ]);
        }
    }
}
