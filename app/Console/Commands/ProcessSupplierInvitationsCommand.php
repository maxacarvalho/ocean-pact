<?php

namespace App\Console\Commands;

use App\Enums\InvitationStatusEnum;
use App\Mail\QuoteCreatedMail;
use App\Models\SupplierInvitation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class ProcessSupplierInvitationsCommand extends Command
{
    protected $signature = 'process-supplier-invitations';

    protected $description = 'Process supplier invitations';

    public function handle(): void
    {
        SupplierInvitation::query()
            ->with(SupplierInvitation::RELATION_SUPPLIER)
            ->where(SupplierInvitation::STATUS, '=', InvitationStatusEnum::PENDING())
            ->each(function (SupplierInvitation $invitation) {
                $supplier = $invitation->supplier;
                $url = URL::signedRoute(
                    'supplier-registration',
                    ['token' => $invitation->token]
                );

                $addresses = collect(explode(';', $supplier->email))->map(function ($email) {
                    return trim($email);
                })->toArray();

                foreach ($addresses as $address) {
                    Mail::to($address)->send(new QuoteCreatedMail($supplier, $url));
                }

                $invitation->update([
                    SupplierInvitation::SENT_AT => now(),
                    SupplierInvitation::STATUS => InvitationStatusEnum::SENT(),
                ]);
            });
    }
}
