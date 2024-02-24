<?php

namespace App\Models\IntegraHub;

use App\Enums\IntegraHub\IntegrationHandlingTypeEnum;
use App\Enums\IntegraHub\IntegrationTypeEnum;
use App\Models\QuotesPortal\Company;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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
 * @property bool                                    $is_synchronous
 * @property bool                                    $allows_duplicates
 * @property array                                   $headers
 * @property array|null                              $path_parameters
 * @property array|null                              $authorization
 * @property int|null                                $interval
 * @property bool                                    $is_running
 * @property Carbon|null                             $last_run_at
 * @property Carbon|null                             $created_at
 * @property Carbon|null                             $updated_at
 * Relations
 * @property-read  Company|null                      $company
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
    public const IS_SYNCHRONOUS = 'is_synchronous';
    public const ALLOWS_DUPLICATES = 'allows_duplicates';
    public const HEADERS = 'headers';
    public const PATH_PARAMETERS = 'path_parameters';
    public const AUTHORIZATION = 'authorization';
    public const INTERVAL = 'interval';
    public const IS_RUNNING = 'is_running';
    public const LAST_RUN_AT = 'last_run_at';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Relations
    public const RELATION_COMPANY = 'company';
    public const RELATION_FIELDS = 'fields';
    public const RELATION_PAYLOADS = 'payloads';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected $casts = [
        self::TYPE => IntegrationTypeEnum::class,
        self::HANDLING_TYPE => IntegrationHandlingTypeEnum::class,
        self::IS_VISIBLE => 'boolean',
        self::IS_ENABLED => 'boolean',
        self::IS_SYNCHRONOUS => 'boolean',
        self::ALLOWS_DUPLICATES => 'boolean',
        self::HEADERS => 'array',
        self::PATH_PARAMETERS => 'array',
        self::AUTHORIZATION => 'array',
        self::IS_RUNNING => 'boolean',
        self::LAST_RUN_AT => 'datetime',
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

    public function fields(): HasMany
    {
        return $this->hasMany(IntegrationTypeField::class);
    }

    public function payloads(): HasMany
    {
        return $this->hasMany(Payload::class);
    }

    public function isSynchronous(): bool
    {
        return $this->is_synchronous;
    }

    public function isForwardable(): bool
    {
        return $this->handling_type->equals(IntegrationHandlingTypeEnum::STORE_AND_SEND);
    }

    public function resolveTargetUrl(Payload $payload): string
    {
        if (! is_array($this->path_parameters) || empty($this->path_parameters)) {
            return $this->target_url;
        }

        $inputPathParameters = collect($payload->path_parameters);
        $targetUrl = $this->target_url;

        return collect($this->path_parameters)
            ->sortByDesc(fn (array $item) => strlen($item['parameter']))
            ->reduce(
                fn (string $targetUrl, array $item) => Str::replace(":{$item['parameter']}", $inputPathParameters->get($item['parameter']), $targetUrl),
                $targetUrl
            );
    }

    public function isDue(): bool
    {
        if (is_null($this->interval) || $this->interval <= 0) {
            return false;
        }

        if (is_null($this->last_run_at)) {
            return true;
        }

        return $this->last_run_at->diffInMinutes(Carbon::now()) >= $this->interval;
    }

    public function markAsRunning(): void
    {
        $this->update([
            self::IS_RUNNING => true,
            self::LAST_RUN_AT => Carbon::now(),
        ]);
    }

    public function markAsStopped(): void
    {
        $this->update([
            self::IS_RUNNING => false,
        ]);
    }

    public function getAuthorizationHeader(): array
    {
        if (is_null($this->authorization) || !$this->authorization['type']) {
            return [];
        }

        if ($this->authorization['type'] === 'basic') {
            return [
                'Authorization' => 'Basic ' . base64_encode($this->authorization['username'] . ':' . $this->authorization['password']),
            ];
        }

        return [];
    }

    public function getHeaders(): array
    {
        return array_merge($this->headers ?? [], $this->getAuthorizationHeader());
    }
}
