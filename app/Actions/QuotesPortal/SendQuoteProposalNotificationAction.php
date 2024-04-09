<?php

namespace App\Actions\QuotesPortal;

use App\Mail\QuotePortal\QuoteProposalCreatedMail;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\Supplier;
use Illuminate\Support\Facades\Mail;

class SendQuoteProposalNotificationAction
{
    public function handle(Quote $quote): void
    {
        $quote->load([
            Quote::RELATION_COMPANY,
            Quote::RELATION_SUPPLIER => [
                Supplier::RELATION_SELLERS,
            ],
        ]);

        foreach ($quote->supplier->sellers as $seller) {
            if (!$seller->isActive()) {
                continue;
            }

            Mail::to($seller->email)->send(
                new QuoteProposalCreatedMail(
                    supplier_name: $quote->supplier->name,
                    company_business_name: $quote->company->business_name,
                    quote_number: $quote->quote_number,
                    proposal_number: $quote->proposal_number,
                    url: route('filament.admin.resources.quotes.edit', ['record' => $quote->id])
                )
            );
        }
    }
}
