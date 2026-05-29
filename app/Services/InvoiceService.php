<?php

namespace App\Services;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    public function createForSubscription(Subscription $subscription): Invoice
    {
        $user    = $subscription->user;
        $plan    = $subscription->plan;
        $seller  = config('invoice.seller');
        $taxRate = config('invoice.tax_rate', 19);

        $gross  = round((float) $subscription->amount, 2);
        $net    = round($gross / (1 + $taxRate / 100), 2);
        $tax    = round($gross - $net, 2);

        $invoice = Invoice::create([
            'user_id'         => $user->id,
            'subscription_id' => $subscription->id,
            'invoice_number'  => Invoice::nextNumber(),
            'status'          => 'draft',
            'net_amount'      => $net,
            'tax_rate'        => $taxRate,
            'tax_amount'      => $tax,
            'gross_amount'    => $gross,
            'currency'        => $subscription->currency ?? 'EUR',
            'description'     => 'SEOmaster ' . ($plan->name ?? 'Abonnement'),
            'due_date'        => now()->addDays(config('invoice.payment_days', 14)),
            'issued_at'       => now(),
        ]);

        $this->generateFiles($invoice, $user, $seller);
        $this->sendEmail($invoice, $user);

        $invoice->update(['status' => 'sent']);

        return $invoice;
    }

    private function generateFiles(Invoice $invoice, User $user, array $seller): void
    {
        $data = compact('invoice', 'user', 'seller');

        // PDF
        $pdf     = Pdf::loadView('invoices.pdf', $data);
        $pdfPath = 'invoices/' . $invoice->invoice_number . '.pdf';
        Storage::disk('local')->put($pdfPath, $pdf->output());

        // XRechnung XML
        $xml     = view('invoices.xrechnung', $data)->render();
        $xmlPath = 'invoices/' . $invoice->invoice_number . '.xml';
        Storage::disk('local')->put($xmlPath, $xml);

        $invoice->update(['pdf_path' => $pdfPath, 'xml_path' => $xmlPath]);
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
