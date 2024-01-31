<?php

namespace App\Data\QuotesPortal;

use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\CompanyUser;
use App\Models\QuotesPortal\Product;
use App\Models\QuotesPortal\Quote;
use App\Models\QuotesPortal\QuoteItem;
use App\Models\QuotesPortal\Supplier;
use App\Models\User;
use Spatie\LaravelData\Attributes\MapOutputName;
use Spatie\LaravelData\Data;

class RequestNewOfferData extends Data
{
    public function __construct(
        #[MapOutputName('quote_number')]
        public string $quoteNumber,
        #[MapOutputName('proposal_number')]
        public int $proposalNumber,
        public array $company,
        public array $supplier,
        public array $buyer,
        public array $items,
    ) {
        //
    }

    public static function fromModel(Quote $quote): static
    {
        $quote->load([
            Quote::RELATION_COMPANY,
            Quote::RELATION_SUPPLIER,
            Quote::RELATION_BUYER => [
                User::RELATION_COMPANIES,
            ],
            Quote::RELATION_ITEMS => [
                QuoteItem::RELATION_PRODUCT,
            ],
        ]);

        return new static(
            quoteNumber: $quote->quote_number,
            proposalNumber: $quote->proposal_number,
            company: [
                Company::ID => $quote->company->id,
                Company::CODE => $quote->company->code,
                Company::CODE_BRANCH => $quote->company->code_branch,
            ],
            supplier: [
                Supplier::ID => $quote->supplier->id,
                Supplier::STORE => $quote->supplier->store,
                Supplier::CODE => $quote->supplier->code,
            ],
            buyer: [
                User::ID => $quote->buyer->companies()->where(Company::ID, '=', $quote->company_id)->first()?->buyer_company->{CompanyUser::BUYER_CODE},
            ],
            items: $quote->items->map(function (QuoteItem $quoteItem) {
                return [
                    QuoteItem::ID => $quoteItem->id,
                    QuoteItem::ITEM => $quoteItem->item,
                    QuoteItem::RELATION_PRODUCT => [
                        Product::ID => $quoteItem->product->id,
                        Product::CODE => $quoteItem->product->code,
                    ],
                ];
            })->toArray(),
        );
    }
}
