<?php

namespace App\Services;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class InvoiceService
{
    public function createForSubscription(Subscription $subscription, ?SubscriptionInvoice $payment = null): Invoice
    {
        $payment ??= $subscription->invoices()->where('status', 'paid')->latest('paid_at')->latest('id')->firstOrFail();
        if ($payment->subscription_id !== $subscription->id || $payment->status !== 'paid' || $payment->amount <= 0) {
            throw new \RuntimeException('Eine Rechnung benötigt eine bestätigte Zahlung für dieses Abonnement.');
        }
        return Cache::lock('invoice-payment-' . $payment->id, 180)->block(10, function () use ($subscription, $payment) {
            $invoice = Invoice::where('subscription_invoice_id', $payment->id)->first();
            if ($invoice?->status === 'sent') {
                return $invoice;
            }
            return $this->generateAndSend($subscription, $payment, $invoice);
        });
    }

    private function generateAndSend(Subscription $subscription, SubscriptionInvoice $payment, ?Invoice $invoice): Invoice
    {
        $user    = $subscription->user;
        $plan    = $subscription->plan;
        $seller  = config('invoice.seller');
        $taxRate = config('invoice.tax_rate', 19);

        // Do not create empty drafts or issue invoices with placeholder seller details.
        if (! class_exists(Pdf::class) || blank($seller['company'] ?? null)
            || blank($seller['address'] ?? null) || blank($seller['zip'] ?? null)
            || blank($seller['city'] ?? null)) {
            throw new \RuntimeException('Automatische Rechnungen benötigen den PDF-Renderer und vollständige Rechnungsstellerdaten. Die Zahlung bleibt im Zahlungsprotokoll gespeichert.');
        }
        $gross  = round((float) $payment->amount, 2);
        $net    = round($gross / (1 + $taxRate / 100), 2);
        $tax    = round($gross - $net, 2);

        $periodStart = $payment->paid_at ?? now();
        $periodEnd = $subscription->billing_cycle === 'yearly'
            ? $periodStart->copy()->addYearNoOverflow() : $periodStart->copy()->addMonthNoOverflow();

        $invoice ??= Cache::lock('invoice-number-' . now()->year, 30)->block(10, fn () => Invoice::create([
            'user_id'         => $user->id,
            'subscription_id' => $subscription->id,
            'subscription_invoice_id' => $payment->id,
            'invoice_number'  => Invoice::nextNumber(),
            'status'          => 'draft',
            'net_amount'      => $net,
            'tax_rate'        => $taxRate,
            'tax_amount'      => $tax,
            'gross_amount'    => $gross,
            'currency'        => $payment->currency,
            'description'     => 'SEOmaster ' . ($plan->name ?? 'Abonnement'),
            'due_date'        => $payment->paid_at ?? now(),
            'issued_at'       => now(),
            'paid_at'         => $payment->paid_at,
            'service_period_start' => $periodStart,
            'service_period_end' => $periodEnd,
        ]));

        try {
            $this->generateFiles($invoice, $user, $seller);
            $this->sendEmail($invoice, $user);
            $invoice->update(['status' => 'sent']);
        } catch (\Throwable $e) {
            $invoice->update(['status' => 'error']);
            throw $e;
        }

        return $invoice;
    }

    private function generateFiles(Invoice $invoice, User $user, array $seller): void
    {
        $data = compact('invoice', 'user', 'seller');

        // PDF
        $pdf     = Pdf::loadView('invoices.pdf', $data);
        $pdfPath = 'invoices/' . $invoice->invoice_number . '.pdf';
        if (! Storage::disk('local')->put($pdfPath, $pdf->output())) {
            throw new \RuntimeException('Rechnungs-PDF konnte nicht gespeichert werden.');
        }
        $invoice->update(['pdf_path' => $pdfPath]);
    }

    private function sendEmail(Invoice $invoice, User $user): void
    {
        Mail::to($user->email, $user->billing_name ?: $user->name)
            ->send(new InvoiceMail($invoice));
    }

    public function getPdfContent(Invoice $invoice): string
    {
        return Storage::disk('local')->get($invoice->pdf_path);
    }

    public function getXmlContent(Invoice $invoice): string
    {
        return Storage::disk('local')->get($invoice->xml_path);
    }
}
