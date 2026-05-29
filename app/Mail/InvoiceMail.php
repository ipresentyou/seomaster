<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rechnung ' . $this->invoice->invoice_number . ' – SEOmaster',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'invoice' => $this->invoice,
                'seller'  => config('invoice.seller'),
            ],
        );
    }

    public function attachments(): array
    {
        $service     = app(InvoiceService::class);
        $attachments = [];

        if ($this->invoice->pdf_path) {
            $attachments[] = Attachment::fromData(
                fn() => $service->getPdfContent($this->invoice),
                $this->invoice->invoice_number . '.pdf'
            )->withMime('application/pdf');
        }

        if ($this->invoice->xml_path) {
            $attachments[] = Attachment::fromData(
                fn() => $service->getXmlContent($this->invoice),
                $this->invoice->invoice_number . '_xrechnung.xml'
            )->withMime('application/xml');
        }

        return $attachments;
    }
}
