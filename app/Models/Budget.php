<?php

namespace App\Models;

use App\Enums\BudgetStatusEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int                     $id
 * @property string                  $company_code
 * @property string|null             $company_code_branch
 * @property string                  $budget_number
 * @property BudgetStatusEnum        $status
 * @property Carbon|null             $created_at
 * @property Carbon|null             $updated_at
 * // Relations
 * @property-read Company            $company
 * @property-read Quote[]|Collection $quotes
 */
class Budget extends Model
{
    public const TABLE_NAME = 'budgets';
    public const ID = 'id';
    public const COMPANY_CODE = 'company_code';
    public const COMPANY_CODE_BRANCH = 'company_code_branch';
    public const BUDGET_NUMBER = 'budget_number';
    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Relations
    public const RELATION_COMPANY = 'company';
    public const RELATION_QUOTES = 'quotes';

    protected $table = self::TABLE_NAME;
    protected $guarded = [
        self::ID,
    ];
    protected $casts = [
        self::STATUS => BudgetStatusEnum::class,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class,
            self::COMPANY_CODE,
            Company::CODE
        );
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function markAsClosed(): void
    {
        $this->update([
            self::STATUS => BudgetStatusEnum::CLOSED,
        ]);
    }
}
