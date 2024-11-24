<?php

namespace App\Actions\QuotesPortal;

use App\Mail\QuotePortal\QuoteContactRequestMail;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteContactRequest;
use App\Models\QuotesPortal\Supplier;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CreateQuoteContactRequestAction
{
    public function handle(int $quoteId, int $userId, string $body): void
    {
        /** @var Quote $quote */
        $quote = Quote::query()
            ->with([
                Quote::RELATION_COMPANY,
                Quote::RELATION_SUPPLIER => [
                    Supplier::RELATION_SELLERS,
                ],
            ])
            ->findOrFail($quoteId);

        $recipients = [];

        foreach ($quote->supplier->sellers as $seller) {
            if (! $seller->isActive()) {
                continue;
            }

            $validator = Validator::make(
                data: ['email' => $seller->email],
                rules: ['email' => 'email'],
            );

            if ($validator->fails()) {
                continue;
            }

            $recipients[] = $seller->email;
        }

        if (empty($recipients)) {
            return;
        }

        QuoteContactRequest::query()->create([
            QuoteContactRequest::QUOTE_ID => $quote->id,
            QuoteContactRequest::USER_ID => $userId,
            QuoteContactRequest::BODY => $body,
            QuoteContactRequest::RECIPIENTS => $recipients,
        ]);

        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(
                new QuoteContactRequestMail(
                    company_business_name: $quote->company->business_name,
                    quote_number: $quote->quote_number,
                    body: $body,
                )
            );
        }
    }
}
