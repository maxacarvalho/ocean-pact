<?php

namespace App\Models;

use App\Enums\IntegrationTypeFieldTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int                          $id
 * @property int                          $integration_type_id
 * @property string                       $field_name
 * @property IntegrationTypeFieldTypeEnum $field_type
 * @property array                        $field_rules
 * @property ?Carbon                      $created_at
 * @property ?Carbon                      $updated_at
 */
class IntegrationTypeField extends Model
{
    public const TABLE_NAME = 'integration_type_fields';
    public const ID = 'id';
    public const INTEGRATION_TYPE_ID = 'integration_type_id';
    public const FIELD_NAME = 'field_name';
    public const FIELD_TYPE = 'field_type';
    public const FIELD_RULES = 'field_rules';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
    ];

    public static array $columns = [
        self::ID,
        self::INTEGRATION_TYPE_ID,
        self::FIELD_NAME,
        self::FIELD_TYPE,
        self::FIELD_RULES,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected $casts = [
        self::FIELD_TYPE => IntegrationTypeFieldTypeEnum::class,
        self::FIELD_RULES => 'array',
    ];

    public function integrationType(): BelongsTo
    {
        return $this->belongsTo(IntegrationType::class);
    }
}
