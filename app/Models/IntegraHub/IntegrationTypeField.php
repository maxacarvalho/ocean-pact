<?php

namespace App\Models\IntegraHub;

use App\Enums\IntegraHub\IntegrationTypeFieldTypeEnum;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * @property int $id
 * @property int $integration_type_id
 * @property int $order_column
 * @property string $field_name
 * @property IntegrationTypeFieldTypeEnum $field_type
 * @property array $field_rules
 * @property ?IntegrationTypeField $target_integration_type_field_id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class IntegrationTypeField extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    public const TABLE_NAME = 'integration_type_fields';

    public const ID = 'id';

    public const INTEGRATION_TYPE_ID = 'integration_type_id';

    public const ORDER_COLUMN = 'order_column';

    public const FIELD_NAME = 'field_name';

    public const FIELD_TYPE = 'field_type';

    public const FIELD_RULES = 'field_rules';

    public const TARGET_INTEGRATION_TYPE_FIELD_ID = 'target_integration_type_field_id';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected function casts(): array
    {
        return [
            self::FIELD_TYPE => IntegrationTypeFieldTypeEnum::class,
            self::FIELD_RULES => 'array',
        ];
    }

    public function integrationType(): BelongsTo
    {
        return $this->belongsTo(IntegrationType::class);
    }

    public function targetIntegrationTypeField(): BelongsTo
    {
        return $this->belongsTo(IntegrationTypeField::class, self::TARGET_INTEGRATION_TYPE_FIELD_ID);
    }

    public function buildSortQuery(): Builder
    {
        return static::query()->where(self::INTEGRATION_TYPE_ID, $this->integration_type_id);
    }
}
