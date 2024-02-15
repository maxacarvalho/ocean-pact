<?php

namespace App\Mail\QuotePortal;

use App\Utils\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteContactRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $company_business_name,
        public readonly string $quote_number,
        public readonly string $body
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: Str::ucfirst(__('quote.emails.contact_request_subject', ['quote' => $this->quote_number])),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.quote-contact-request',
            with: [
                'quote' => $this->quote_number,
                'company_name' => $this->company_business_name,
                'body' => $this->body,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
