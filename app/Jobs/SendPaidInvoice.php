<?php

namespace App\Jobs;

use App\Models\SubscriptionInvoice;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPaidInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;
    public int $backoff = 60;

    public function __construct(public int $paymentId) {}

    public function handle(InvoiceService $service): void
    {
        if (! config('invoice.enabled')) {
            return;
        }
        $payment = SubscriptionInvoice::findOrFail($this->paymentId);
        $service->createForSubscription($payment->subscription, $payment);
    }
}
