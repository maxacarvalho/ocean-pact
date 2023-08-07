<?php

namespace App\Models;

use App\Enums\IntegrationHandlingTypeEnum;
use App\Enums\IntegrationTypeEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * @property int                                     $id
 * @property int|null                                $company_id
 * @property string                                  $code
 * @property string                                  $description
 * @property IntegrationTypeEnum                     $type
 * @property IntegrationHandlingTypeEnum             $handling_type
 * @property string                                  $target_url
 * @property bool                                    $is_visible
 * @property bool                                    $is_enabled
 * @property bool                                    $is_protected
 * @property bool                                    $is_synchronous
 * @property bool                                    $allows_duplicates
 * @property string|null                             $processor
 * @property Carbon|null                             $created_at
 * @property Carbon|null                             $updated_at
 * @property-read Company|null                       $company
 * @property-read  IntegrationTypeField[]|Collection $fields
 * @property-read  Payload[]|Collection              $payloads
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
    public const IS_VISIBLE = 'is_visible';
    public const IS_ENABLED = 'is_enabled';
    public const IS_PROTECTED = 'is_protected';
    public const IS_SYNCHRONOUS = 'is_synchronous';
    public const ALLOWS_DUPLICATES = 'allows_duplicates';
    public const PROCESSOR = 'processor';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Relationships
    public const RELATION_COMPANY = 'company';
    public const RELATION_FIELDS = 'fields';
    public const RELATION_PAYLOADS = 'payloads';

    // Protected integration types
    public const INTEGRATION_ANSWERED_QUOTES = 'cotacoes-respondidas';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
    ];

    protected $casts = [
        self::TYPE => IntegrationTypeEnum::class,
        self::HANDLING_TYPE => IntegrationHandlingTypeEnum::class,
        self::IS_VISIBLE => 'boolean',
        self::IS_ENABLED => 'boolean',
        self::IS_PROTECTED => 'boolean',
        self::IS_SYNCHRONOUS => 'boolean',
        self::ALLOWS_DUPLICATES => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(static function (self $model) {
            $model->code = $model->code ?? Str::slug($model->description);
        });

        static::deleting(function (self $integrationType) {
            if ($integrationType->is_protected) {
                throw new RuntimeException('Cannot delete a protected integration type');
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(IntegrationTypeField::class);
    }

    public function payloads(): HasMany
    {
        return $this->hasMany(Payload::class);
    }

    public function isProcessable(): bool
    {
        return $this->is_enabled && $this->processor;
    }

    public function isSynchronous(): bool
    {
        return $this->is_synchronous;
    }

    public function getProcessor(): ?string
    {
        return $this->processor;
    }
}
