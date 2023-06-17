<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Supplier;
use App\Utils\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteCreatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Supplier $supplier,
        public readonly Company $company,
        public readonly string $url
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: Str::ucfirst(__('invitation.new_quote_ready_for_reply', ['company' => $this->company->business_name])),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.quote-created',
            with: [
                'greetings' => Str::title(__('invitation.greetings', ['name' => $this->supplier->name])),
                'body' => Str::ucfirst(__('invitation.click_below_to_reply_the_quote')),
                'button' => Str::formatTitle(__('invitation.reply_quote')),
                'url' => $this->url,
                'company_name' => $this->company->business_name,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
