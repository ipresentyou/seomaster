<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
        'user_id', 'subscription_id', 'invoice_number', 'status',
        'net_amount', 'tax_rate', 'tax_amount', 'gross_amount',
        'currency', 'description', 'pdf_path', 'xml_path',
        'due_date', 'issued_at',
    ];

    protected $casts = [
        'net_amount'   => 'float',
        'tax_rate'     => 'float',
        'tax_amount'   => 'float',
        'gross_amount' => 'float',
        'due_date'     => 'date',
        'issued_at'    => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public static function nextNumber(): string
    {
        $year  = now()->year;
        $count = static::whereYear('created_at', $year)->count() + 1;
        return sprintf('RE-%d-%04d', $year, $count);
    }
}
