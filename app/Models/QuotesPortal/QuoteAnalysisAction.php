<?php

namespace App\Models\QuotesPortal;

use App\Enums\QuotesPortal\QuoteAnalysisActionEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int                     $id
 * @property QuoteAnalysisActionEnum $action
 * @property int                     $quote_id
 * @property Carbon                  $created_at
 * @property Carbon                  $updated_at
 * @property-read Quote              $quote
 */
class QuoteAnalysisAction extends Model
{
    public const TABLE_NAME = 'quote_analysis_actions';
    public const ID = 'id';
    public const ACTION = 'action';
    public const QUOTE_ID = 'quote_id';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Relations
    public const RELATION_QUOTE = 'quote';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected $casts = [
        self::ACTION => QuoteAnalysisActionEnum::class,
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
}
