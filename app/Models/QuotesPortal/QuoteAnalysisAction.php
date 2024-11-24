<?php

namespace App\Models\QuotesPortal;

use App\Enums\QuotesPortal\QuoteAnalysisActionEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property QuoteAnalysisActionEnum $action
 * @property int $quote_id
 * @property string $quote_number
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Quote              $quote
 */
class QuoteAnalysisAction extends Model
{
    public const string TABLE_NAME = 'quote_analysis_actions';

    public const string ID = 'id';

    public const string ACTION = 'action';

    public const string QUOTE_ID = 'quote_id';

    public const string QUOTE_NUMBER = 'quote_number';

    public const string CREATED_AT = 'created_at';

    public const string UPDATED_AT = 'updated_at';

    // Relations
    public const string RELATION_QUOTE = 'quote';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected function casts(): array
    {
        return [
            self::ACTION => QuoteAnalysisActionEnum::class,
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
}
