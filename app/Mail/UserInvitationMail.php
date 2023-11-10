<?php

namespace App\Mail;

use App\Models\User;
use App\Utils\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly User $user,
        public readonly string $url
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: Str::ucfirst(__('invitation.buyer_invitation_to_join_ocean_pact_quotes_portal')),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.buyer-user-created',
            with: [
                'greetings' => Str::title(__('invitation.greetings', ['name' => $this->user->name])),
                'body' => Str::ucfirst(__('invitation.click_the_button_below_to_finish_your_registration')),
                'button' => Str::formatTitle(__('invitation.finish_registration')),
                'url' => $this->url,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
