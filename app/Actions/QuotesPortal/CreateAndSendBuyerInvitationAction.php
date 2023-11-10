<?php

namespace App\Actions\QuotesPortal;

use App\Enums\QuotesPortal\InvitationStatusEnum;
use App\Mail\BuyerUserCreatedMail;
use App\Models\QuotesPortal\BuyerInvitation;
use App\Models\User;
use App\Utils\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class CreateAndSendBuyerInvitationAction
{
    public function handle(User $buyer): void
    {
        /** @var BuyerInvitation $buyerInvitation */
        $buyerInvitation = BuyerInvitation::query()
            ->create([
                BuyerInvitation::BUYER_ID => $buyer->id,
                BuyerInvitation::TOKEN => Str::uuid(),
            ]);

        $url = URL::temporarySignedRoute(
            'filament.admin.buyer-registration',
            now()->addDays(5),
            ['token' => $buyerInvitation->token]
        );

        Mail::to($buyer)->send(new BuyerUserCreatedMail($buyer, $url));

        $buyerInvitation->update([
            BuyerInvitation::SENT_AT => now(),
            BuyerInvitation::STATUS => InvitationStatusEnum::SENT,
        ]);
    }
}
