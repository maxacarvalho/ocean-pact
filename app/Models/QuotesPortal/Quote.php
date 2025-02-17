<?php

namespace App\Models\QuotesPortal;

use App\Enums\QuotesPortal\FreightTypeEnum;
use App\Enums\QuotesPortal\QuoteStatusEnum;
use App\Models\Role;
use App\Models\User;
use App\Utils\Str;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|string $proposal_number
 * @property int $company_id
 * @property string $company_code
 * @property string|null $company_code_branch
 * @property int $supplier_id
 * @property int $payment_condition_id
 * @property int $buyer_id
 * @property int $budget_id
 * @property string $quote_number
 * @property Carbon|null $valid_until
 * @property QuoteStatusEnum $status
 * @property string|null $comments
 * @property int $expenses
 * @property int $freight_cost
 * @property FreightTypeEnum|null $freight_type
 * @property int|null $currency_id
 * @property int|null $replaced_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *                                   Relations
 * @property-read Budget                 $budget
 * @property-read Company                $company
 * @property-read Supplier               $supplier
 * @property-read PaymentCondition       $paymentCondition
 * @property-read User|null              $buyer
 * @property-read QuoteItem[]|Collection $items
 * @property-read Currency|null          $currency
 * @property-read Quote|null             $replacedBy
 * Virtual
 * @property-read int                    $count
 */
class Quote extends Model
{
    public const string TABLE_NAME = 'quotes';

    public const string ID = 'id';

    public const string PROPOSAL_NUMBER = 'proposal_number';

    public const string COMPANY_ID = 'company_id';

    public const string SUPPLIER_ID = 'supplier_id';

    public const string PAYMENT_CONDITION_ID = 'payment_condition_id';

    public const string BUYER_ID = 'buyer_id';

    public const string BUDGET_ID = 'budget_id';

    public const string QUOTE_NUMBER = 'quote_number';

    public const string VALID_UNTIL = 'valid_until';

    public const string STATUS = 'status';

    public const string COMMENTS = 'comments';

    public const string EXPENSES = 'expenses';

    public const string FREIGHT_COST = 'freight_cost';

    public const string FREIGHT_TYPE = 'freight_type';

    public const string CURRENCY_ID = 'currency_id';

    public const string REPLACED_BY = 'replaced_by';

    public const string CREATED_AT = 'created_at';

    public const string UPDATED_AT = 'updated_at';

    // Relations
    public const string RELATION_BUDGET = 'budget';

    public const string RELATION_COMPANY = 'company';

    public const string RELATION_SUPPLIER = 'supplier';

    public const string RELATION_PAYMENT_CONDITION = 'paymentCondition';

    public const string RELATION_BUYER = 'buyer';

    public const string RELATION_ITEMS = 'items';

    public const string RELATION_CURRENCY = 'currency';

    public const string RELATION_REPLACED_BY = 'replacedBy';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected function casts(): array
    {
        return [
            self::STATUS => QuoteStatusEnum::class,
            self::VALID_UNTIL => 'date',
            self::FREIGHT_TYPE => FreightTypeEnum::class,
        ];
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function paymentCondition(): BelongsTo
    {
        return $this->belongsTo(PaymentCondition::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->whereHas(User::RELATION_ROLES, function ($query) {
                $query->where(Role::NAME, Role::ROLE_BUYER);
            });
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function replacedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, self::REPLACED_BY);
    }

    protected function proposalNumber(): Attribute
    {
        return Attribute::make(
            get: fn (int $value) => Str::padLeft($value, 2, '0'),
            set: fn (string $value) => (int) $value,
        );
    }

    public function isResponded(): bool
    {
        return $this->status === QuoteStatusEnum::RESPONDED;
    }

    public function isAnalyzed(): bool
    {
        return $this->status === QuoteStatusEnum::ANALYZED;
    }

    public function canBeResponded(): bool
    {
        return $this->status === QuoteStatusEnum::PENDING;
    }

    public function markAsPending(): void
    {
        $this->update([
            self::STATUS => QuoteStatusEnum::PENDING,
        ]);
    }

    public function markAsResponded(): void
    {
        $this->update([
            self::STATUS => QuoteStatusEnum::RESPONDED,
        ]);
    }

    public function markAsAnalyzed(): void
    {
        $this->update([
            self::STATUS => QuoteStatusEnum::ANALYZED,
        ]);
    }
}
