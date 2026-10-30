<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrialFeedback extends Model
{
    protected $table = 'trial_feedback';

    protected $fillable = ['subscription_id', 'rating', 'reason', 'comment'];

    public const REASONS = [
        'subscribed' => 'Ich nutze SEOmaster weiter',
        'price' => 'Der Preis passt nicht',
        'setup' => 'Die Einrichtung war zu schwierig',
        'quality' => 'Die Ergebnisse haben mich nicht überzeugt',
        'features' => 'Mir fehlen Funktionen',
        'time' => 'Ich hatte noch keine Zeit zum Testen',
        'no_need' => 'Ich habe aktuell keinen Bedarf',
        'other' => 'Ein anderer Grund',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
