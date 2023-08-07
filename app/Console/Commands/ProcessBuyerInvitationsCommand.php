<?php

namespace App\Console\Commands;

use App\Enums\InvitationStatusEnum;
use App\Mail\BuyerUserCreatedMail;
use App\Models\BuyerInvitation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class ProcessBuyerInvitationsCommand extends Command
{
    protected $signature = 'process-buyer-invitations';

    protected $description = 'Processes buyer invitations';

    public function handle(): void
    {
        BuyerInvitation::query()
            ->with(BuyerInvitation::RELATION_BUYER)
            ->where(BuyerInvitation::STATUS, '=', InvitationStatusEnum::PENDING)
            ->each(function (BuyerInvitation $invitation) {
                $user = $invitation->buyer;
                $url = URL::temporarySignedRoute(
                    'buyer-registration',
                    now()->addDays(5),
                    ['token' => $invitation->token]
                );

                Mail::to($user)->send(new BuyerUserCreatedMail($user, $url));

                $invitation->update([
                    BuyerInvitation::SENT_AT => now(),
                    BuyerInvitation::STATUS => InvitationStatusEnum::SENT,
                ]);
            });
    }
}
