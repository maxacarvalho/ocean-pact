<?php

namespace App\Models\IntegraHub;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int        $id
 * @property int        $integration_type_id
 * @property array      $settings
 * @property ?Carbon    $created_at
 * @property ?Carbon    $updated_at
 * Relations
 * @property-read IntegrationType $integrationType
 */
class IntegrationTypeFrequency extends Model {
    public const TABLE_NAME = 'integration_type_fields';
    public const ID = 'id';
    public const INTEGRATION_TYPE_ID = 'integration_type_id';
    public const SETTINGS = 'settings';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected $casts = [
        self::SETTINGS => 'array',
    ];

    public function integrationType(): BelongsTo
    {
        return $this->belongsTo(IntegrationType::class);
    }
}
