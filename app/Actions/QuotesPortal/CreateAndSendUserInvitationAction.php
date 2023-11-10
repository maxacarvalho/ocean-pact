<?php

namespace App\Actions\QuotesPortal;

use App\Mail\QuoteCreatedMail;
use App\Mail\UserInvitationMail;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\Supplier;
use App\Models\QuotesPortal\UserInvitation;
use App\Models\User;
use App\Utils\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class CreateAndSendUserInvitationAction
{
    public function handle(User $buyer = null, Quote $quote = null): void
    {
        if ($buyer) {
            $this->createAndSendNotification($buyer);
        }

        if ($quote) {
            $this->sendSellerUserInvites($quote);
        }
    }

    private function sendSellerUserInvites(Quote $quote): void
    {
        $quote->load([
            Quote::RELATION_COMPANY,
            Quote::RELATION_SUPPLIER => [
                Supplier::RELATION_SELLERS,
            ],
        ]);

        foreach ($quote->supplier->sellers as $seller) {
            if (! $seller->isActive()) {
                continue;
            }

            $userInvitation = $this->createUserInvitation($seller, $quote);

            Mail::to($seller->email)->send(
                new QuoteCreatedMail(
                    $quote->supplier->name,
                    $quote->company->business_name,
                    $quote->quote_number,
                    $this->getUrl($userInvitation)
                )
            );

            $userInvitation->markAsSent();
        }
    }

    private function createAndSendNotification(User $user): void
    {
        $userInvitation = $this->createUserInvitation($user);

        $url = $this->getUrl($userInvitation);

        Mail::to($user)->send(new UserInvitationMail($user, $url));

        $userInvitation->markAsSent();
    }

    private function createUserInvitation(User $user, Quote $quote = null): UserInvitation
    {
        /** @var UserInvitation $userInvitation */
        $userInvitation = UserInvitation::query()
            ->create([
                UserInvitation::USER_ID => $user->id,
                UserInvitation::TOKEN => Str::uuid(),
                UserInvitation::QUOTE_ID => $quote?->id,
            ]);

        return $userInvitation;
    }

    private function getUrl(UserInvitation $userInvitation): string
    {
        return URL::temporarySignedRoute(
            'filament.admin.user-activation',
            now()->addDays(5),
            ['token' => $userInvitation->token]
        );
    }
}
