<?php

namespace App\Mail\QuotePortal;

use App\Utils\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseRequestGeneratedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $supplier_name,
        public readonly string $company_business_name,
        public readonly string $purchase_request_number,
        public readonly string $url
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: Str::ucfirst(__('purchase_request.supplier_email.new_purchase_request_ready_for_reply', ['company' => $this->company_business_name])),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.purchase-request-generated',
            with: [
                'greetings' => Str::title(__('purchase_request.supplier_email.greetings', ['name' => $this->supplier_name])),
                'body' => Str::ucfirst(__('purchase_request.supplier_email.click_below_to_view_the_purchase_request')),
                'button' => Str::formatTitle(__('purchase_request.supplier_email.button')),
                'url' => $this->url,
                'company_name' => $this->company_business_name,
                'purchase_request' => Str::ucfirst(__('purchase_request.supplier_email.purchase_request', ['purchase_request_number' => $this->purchase_request_number])),
            ]
        );
    }
}
