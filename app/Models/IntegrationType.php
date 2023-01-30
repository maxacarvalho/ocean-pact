<?php

namespace App\Models;

use App\Enums\IntegrationHandlingTypeEnum;
use App\Enums\IntegrationTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int                         $id
 * @property int|null                    $company_id
 * @property string                      $code
 * @property string                      $description
 * @property IntegrationTypeEnum         $type
 * @property IntegrationHandlingTypeEnum $handling_type
 * @property string                      $target_url
 */
class IntegrationType extends Model
{
    public const TABLE_NAME = 'integration_types';
    public const ID = 'id';
    public const COMPANY_ID = 'company_id';
    public const CODE = 'code';
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

    protected static function booted(): void
    {
        static::creating(static function (self $model) {
            $model->code = $model->code ?? Str::slug($model->description);
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
