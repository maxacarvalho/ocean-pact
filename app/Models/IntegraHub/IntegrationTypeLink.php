<?php

namespace App\Models\IntegraHub;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegrationTypeLink extends Model
{
    use HasFactory;

    public const TABLE_NAME = 'integration_type_links';
    public const ID = 'id';
    public const INTEGRATION_TYPE_ID = 'integration_type_id';
    public const LINKED_INTEGRATION_TYPE_ID = 'linked_integration_type_id';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    public function integrationType(): BelongsTo
    {
        return $this->belongsTo(IntegrationType::class);
    }

    public function linkedIntegrationType(): BelongsTo
    {
        return $this->belongsTo(IntegrationType::class, 'linked_integration_type_id');
    }
}
