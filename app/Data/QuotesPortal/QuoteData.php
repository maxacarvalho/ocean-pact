<?php

namespace App\Data\QuotesPortal;

use App\Enums\QuotesPortal\FreightTypeEnum;
use App\Enums\QuotesPortal\QuoteStatusEnum;
use App\Http\Requests\QuotesPortal\StoreQuoteRequest;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\Quote;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;

class QuoteData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly string|Optional $proposal_number,
        public readonly int $company_id,
        public readonly int|Optional $supplier_id,
        public readonly int|Optional $payment_condition_id,
        public readonly int|Optional $buyer_id,
        public readonly int|Optional $budget_id,
        public readonly string $quote_number,
        public readonly Carbon|null $valid_until,
        #[WithCast(EnumCast::class)]
        public readonly QuoteStatusEnum|Optional $status,
        public readonly string|null $comments,
        public readonly int|Optional $expenses,
        public readonly int|Optional $freight_cost,
        #[WithCast(EnumCast::class)]
        public readonly FreightTypeEnum|null $freight_type,
        public readonly int|null $currency_id,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
        // Relations
        public readonly Lazy|BudgetData $budget,
        public readonly Lazy|CompanyData|Optional $company,
        public readonly Lazy|SupplierData $supplier,
        public readonly Lazy|PaymentConditionData|Optional $paymentCondition,
        public readonly Lazy|BuyerData $buyer,
        public readonly Lazy|CurrencyData $currency,
        #[DataCollectionOf(QuoteItemData::class)]
        public readonly Lazy|DataCollection $items,
    ) {
        //
    }

    public static function fromModel(Quote $quote): self
    {
        return new self(
            id: $quote->id,
            proposal_number: $quote->proposal_number,
            company_id: $quote->company_id,
            supplier_id: $quote->supplier_id,
            payment_condition_id: $quote->payment_condition_id,
            buyer_id: $quote->buyer_id,
            budget_id: $quote->budget_id,
            quote_number: $quote->quote_number,
            valid_until: $quote->valid_until,
            status: $quote->status,
            comments: $quote->comments,
            expenses: $quote->expenses,
            freight_cost: $quote->freight_cost,
            freight_type: $quote->freight_type,
            currency_id: $quote->currency_id,
            created_at: $quote->created_at,
            updated_at: $quote->updated_at,
            // Relations
            budget: Lazy::whenLoaded(Quote::RELATION_BUDGET, $quote, static fn () => BudgetData::from($quote->budget)),
            company: Lazy::whenLoaded(Quote::RELATION_COMPANY, $quote, static fn () => CompanyData::from($quote->company)),
            supplier: Lazy::whenLoaded(Quote::RELATION_SUPPLIER, $quote, static fn () => SupplierData::from($quote->supplier)),
            paymentCondition: Lazy::whenLoaded(Quote::RELATION_PAYMENT_CONDITION, $quote, static fn () => PaymentConditionData::from($quote->paymentCondition)),
            buyer: Lazy::whenLoaded(Quote::RELATION_BUYER, $quote, static fn () => BuyerData::fromQuote($quote)),
            currency: Lazy::whenLoaded(Quote::RELATION_CURRENCY, $quote, static fn () => CurrencyData::from($quote->currency)),
            items: Lazy::whenLoaded(Quote::RELATION_ITEMS, $quote, static fn () => QuoteItemData::collection($quote->items)),
        );
    }

    public static function fromStoreQuoteRequest(StoreQuoteRequest $request): QuoteData
    {
        /** @var Company $company */
        $company = Company::query()
            ->where(Company::CODE, '=', $request->input('company_code'))
            ->where(Company::CODE_BRANCH, '=', $request->input('company_code_branch'))
            ->firstOrFail();

        return static::from([
            ...$request->validated(),
            'company_id' => $company->id,
        ]);
    }
}
