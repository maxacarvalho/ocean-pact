<?php

namespace App\Models\QuotesPortal;

use App\Enums\QuotesPortal\BudgetStatusEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $company_code
 * @property string|null $company_code_branch
 * @property string $budget_number
 * @property BudgetStatusEnum $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *                                   Relations
 * @property-read Company            $company
 * @property-read Quote[]|Collection $quotes
 */
class Budget extends Model
{
    public const string TABLE_NAME = 'budgets';

    public const string ID = 'id';

    public const string COMPANY_CODE = 'company_code';

    public const string COMPANY_CODE_BRANCH = 'company_code_branch';

    public const string BUDGET_NUMBER = 'budget_number';

    public const string STATUS = 'status';

    public const string CREATED_AT = 'created_at';

    public const string UPDATED_AT = 'updated_at';

    // Relations
    public const string RELATION_COMPANY = 'company';

    public const string RELATION_QUOTES = 'quotes';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected function casts(): array
    {
        return [
            self::STATUS => BudgetStatusEnum::class,
        ];
    }

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
