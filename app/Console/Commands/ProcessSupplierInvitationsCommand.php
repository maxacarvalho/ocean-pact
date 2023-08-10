<?php

namespace App\Console\Commands;

use App\Enums\InvitationStatusEnum;
use App\Mail\QuoteCreatedMail;
use App\Models\Quote;
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
            ->with(SupplierInvitation::RELATION_SUPPLIER, SupplierInvitation::RELATION_QUOTE.'.'.Quote::RELATION_COMPANY)
            ->where(SupplierInvitation::STATUS, '=', InvitationStatusEnum::PENDING)
            ->each(function (SupplierInvitation $invitation) {
                $supplier = $invitation->supplier;
                $quote = $invitation->quote;

                $url = URL::signedRoute(
                    'filament.admin.auth.login',
                    ['token' => $invitation->token]
                );

                $addresses = collect(explode(';', $supplier->email))->map(function ($email) {
                    return trim($email);
                })->toArray();

                foreach ($addresses as $address) {
                    Mail::to($address)->send(
                        new QuoteCreatedMail(
                            $supplier->name,
                            $quote->company->business_name,
                            $quote->quote_number,
                            $url
                        )
                    );
                }

                $invitation->update([
                    SupplierInvitation::SENT_AT => now(),
                    SupplierInvitation::STATUS => InvitationStatusEnum::SENT,
                ]);
            });
    }
}
