<?php

namespace App\Models\QuotesPortal;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property string      $company_code
 * @property int         $protheus_currency_id
 * @property string      $description
 * @property string      $protheus_code
 * @property string      $protheus_acronym
 * @property string      $iso_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Currency extends Model
{
    public const TABLE_NAME = 'currencies';
    public const ID = 'id';
    public const COMPANY_CODE = 'company_code';
    public const PROTHEUS_CURRENCY_ID = 'protheus_currency_id';
    public const DESCRIPTION = 'description';
    public const PROTHEUS_CODE = 'protheus_code';
    public const PROTHEUS_ACRONYM = 'protheus_acronym';
    public const ISO_CODE = 'iso_code';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    protected $primaryKey = self::ID;

    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];
}
