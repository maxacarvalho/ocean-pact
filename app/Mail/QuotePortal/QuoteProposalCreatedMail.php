<?php

namespace App\Mail\QuotePortal;

use App\Utils\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteProposalCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $supplier_name,
        public readonly string $company_business_name,
        public readonly string $quote_number,
        public readonly string $url
    ) {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: Str::ucfirst(__('quote.emails.new_quote_proposal_requested', [
                'quote' => $this->quote_number,
                'company' => $this->company_business_name,
            ])),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.quote-proposal-created',
            with: [
                'greetings' => Str::title(__('quote.emails.greetings', ['name' => $this->supplier_name])),
                'body' => Str::ucfirst(__('quote.emails.click_below_to_reply_the_quote')),
                'button' => Str::formatTitle(__('quote.emails.reply_quote')),
                'url' => $this->url,
                'company_name' => $this->company_business_name,
                'quote' => Str::ucfirst(__('quote.emails.quote_number', ['quote_number' => $this->quote_number])),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
