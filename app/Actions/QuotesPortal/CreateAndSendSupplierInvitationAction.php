<?php

namespace App\Actions\QuotesPortal;

use App\Enums\QuotesPortal\InvitationStatusEnum;
use App\Mail\QuoteCreatedMail;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\SupplierInvitation;
use App\Utils\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class CreateAndSendSupplierInvitationAction
{
    public function handle(Quote $quote): void
    {
        $supplier = $quote->supplier;

        /** @var SupplierInvitation $supplierInvitation */
        $supplierInvitation = SupplierInvitation::query()
            ->create([
                SupplierInvitation::SUPPLIER_ID => $supplier->id,
                SupplierInvitation::QUOTE_ID => $quote->id,
                SupplierInvitation::TOKEN => Str::uuid(),
            ]);

        $url = URL::signedRoute(
            'filament.admin.auth.login',
            ['token' => $supplierInvitation->token]
        );

        foreach ($supplier->sellers as $seller) {
            if (! $seller->isActive()) {
                continue;
            }

            Mail::to($seller->email)->send(
                new QuoteCreatedMail(
                    $supplier->name,
                    $quote->company->business_name,
                    $quote->quote_number,
                    $quote->proposal_number,
                    $url
                )
            );
        }

        $supplierInvitation->update([
            SupplierInvitation::SENT_AT => now(),
            SupplierInvitation::STATUS => InvitationStatusEnum::SENT,
        ]);
    }
}
