<?php

namespace App\Models;

use App\Enums\QuoteStatusEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int                         $id
 * @property string                      $company_code
 * @property string|null                 $company_code_branch
 * @property int                         $supplier_id
 * @property int                         $payment_condition_id
 * @property int                         $buyer_id
 * @property int                         $budget_id
 * @property string                      $quote_number
 * @property Carbon|null                 $valid_until
 * @property QuoteStatusEnum             $status
 * @property string|null                 $comments
 * @property Carbon|null                 $created_at
 * @property Carbon|null                 $updated_at
 * // Relations
 * @property-read Budget                 $budget
 * @property-read Company                $company
 * @property-read Supplier               $supplier
 * @property-read PaymentCondition       $paymentCondition
 * @property-read User|null              $buyer
 * @property-read QuoteItem[]|Collection $items
 */
class Quote extends Model
{
    public const TABLE_NAME = 'quotes';
    public const ID = 'id';
    public const COMPANY_ID = 'company_id';
    public const COMPANY_CODE = 'company_code';
    public const COMPANY_CODE_BRANCH = 'company_code_branch';
    public const BUDGET_ID = 'budget_id';
    public const SUPPLIER_ID = 'supplier_id';
    public const PAYMENT_CONDITION_ID = 'payment_condition_id';
    public const BUYER_ID = 'buyer_id';
    public const QUOTE_NUMBER = 'quote_number';
    public const VALID_UNTIL = 'valid_until';
    public const STATUS = 'status';
    public const COMMENTS = 'comments';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Relations
    public const RELATION_BUDGET = 'budget';
    public const RELATION_COMPANY = 'company';
    public const RELATION_SUPPLIER = 'supplier';
    public const RELATION_PAYMENT_CONDITION = 'paymentCondition';
    public const RELATION_BUYER = 'buyer';
    public const RELATION_ITEMS = 'items';

    protected $table = self::TABLE_NAME;
    protected $guarded = [
        self::ID,
    ];
    protected $casts = [
        self::STATUS => QuoteStatusEnum::class,
        self::VALID_UNTIL => 'date',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class,
            self::COMPANY_CODE,
            Company::CODE
        );
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

    public function isResponded(): bool
    {
        return $this->status->equals(QuoteStatusEnum::RESPONDED());
    }

    public function canBeResponded(): bool
    {
        return ! $this->status->equals(QuoteStatusEnum::RESPONDED());
    }

    public function markAsResponded(): void
    {
        $this->update([
            self::STATUS => QuoteStatusEnum::RESPONDED(),
        ]);
    }
}
