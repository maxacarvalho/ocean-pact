<?php

namespace App\Data\QuotesPortal;

use App\Casts\QuotesPortal\MoneyCast;
use App\Enums\QuotesPortal\QuoteItemStatusEnum;
use App\Models\QuotesPortal\QuoteItem;
use Brick\Money\Money;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;

class QuoteItemData extends Data
{
    public function __construct(
        public readonly int|Optional $id,
        public readonly int|Optional $quote_id,
        public readonly int|Optional $product_id,
        public readonly string $description,
        public readonly string $measurement_unit,
        public readonly string $item,
        public readonly float $quantity,
        #[WithCast(MoneyCast::class)]
        public readonly Money $unit_price,
        public readonly string|Optional $currency,
        public readonly float $ipi,
        public readonly float $icms,
        public readonly int|Optional $delivery_in_days,
        public readonly bool|Optional $should_be_quoted,
        #[WithCast(EnumCast::class)]
        public readonly QuoteItemStatusEnum|Optional $status,
        public readonly ?string $comments,
        public readonly ?string $seller_image,
        public readonly ?string $buyer_image,
        public readonly Carbon|null|Optional $created_at,
        public readonly Carbon|null|Optional $updated_at,
        // Relations
        public readonly Lazy|ProductData $product,
    ) {
        //
    }

    public static function fromModel(QuoteItem $quoteItem): self
    {
        return new self(
            id: $quoteItem->id,
            quote_id: $quoteItem->quote_id,
            product_id: $quoteItem->product_id,
            description: $quoteItem->description,
            measurement_unit: $quoteItem->measurement_unit,
            item: $quoteItem->item,
            quantity: $quoteItem->quantity,
            unit_price: $quoteItem->unit_price,
            currency: $quoteItem->currency,
            ipi: $quoteItem->ipi,
            icms: $quoteItem->icms,
            delivery_in_days: $quoteItem->delivery_in_days,
            should_be_quoted: $quoteItem->should_be_quoted,
            status: $quoteItem->status,
            comments: $quoteItem->comments,
            seller_image: $quoteItem->seller_image,
            buyer_image: $quoteItem->buyer_image,
            created_at: $quoteItem->created_at,
            updated_at: $quoteItem->updated_at,
            product: Lazy::whenLoaded(QuoteItem::RELATION_PRODUCT, $quoteItem, static fn () => ProductData::from($quoteItem->product)),
        );
    }
}
