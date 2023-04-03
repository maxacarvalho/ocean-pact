<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int                   $id
 * @property int                   $company_id
 * @property int                   $budget_id
 * @property string                $budget_number
 * @property string                $quote_number
 * @property string|null           $comments
 * @property Carbon|null           $created_at
 * @property Carbon|null           $updated_at
 * @property-read Company          $company
 * @property-read Budget           $budget
 * @property-read Supplier         $supplier
 * @property-read PaymentCondition $paymentCondition
 * @property-read User|null        $buyer
 */
class Quote extends Model
{
    public const TABLE_NAME = 'quotes';
    public const ID = 'id';
    public const COMPANY_ID = 'company_id';
    public const BUDGET_ID = 'budget_id';
    public const SUPPLIER_ID = 'supplier_id';
    public const PAYMENT_CONDITION_ID = 'payment_condition_id';
    public const BUYER_ID = 'buyer_id';
    public const QUOTE_NUMBER = 'quote_number';
    public const COMMENTS = 'comments';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Relations
    public const RELATION_COMPANY = 'company';
    public const RELATION_BUDGET = 'budget';
    public const RELATION_SUPPLIER = 'supplier';
    public const RELATION_PAYMENT_CONDITION = 'paymentCondition';
    public const RELATION_BUYER = 'buyer';
    public const RELATION_ITEMS = 'items';

    protected $table = self::TABLE_NAME;
    protected $guarded = [
        self::ID,
    ];

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
}
