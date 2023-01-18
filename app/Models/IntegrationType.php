<?php

namespace App\Models;

use App\Enums\IntegrationHandlingTypeEnum;
use App\Enums\IntegrationTypeEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int                         $id
 * @property string                      $description
 * @property IntegrationTypeEnum         $type
 * @property IntegrationHandlingTypeEnum $handling_type
 * @property string                      $target_url
 */
class IntegrationType extends Model
{
    public const TABLE_NAME = 'integration_types';
    public const ID = 'id';
    public const DESCRIPTION = 'description';
    public const TYPE = 'type';
    public const HANDLING_TYPE = 'handling_type';
    public const TARGET_URL = 'target_url';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
    ];

    public static array $columns = [
        self::ID,
        self::DESCRIPTION,
        self::TYPE,
        self::HANDLING_TYPE,
        self::TARGET_URL,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected $casts = [
        self::TYPE => IntegrationTypeEnum::class,
        self::HANDLING_TYPE => IntegrationHandlingTypeEnum::class,
    ];
}
