<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int         $id
 * @property string      $code
 * @property string      $branch
 * @property string      $cnpj
 * @property string      $description
 * @property string      $legal_name
 * @property string      $trade_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User[]|Collection $users
 */
class Company extends Model
{
    use HasFactory;

    public const TABLE_NAME = 'companies';
    public const ID = 'id';
    public const CODE = 'code';
    public const BRANCH = 'branch';
    public const CNPJ = 'cnpj';
    public const DESCRIPTION = 'description';
    public const LEGAL_NAME = 'legal_name';
    public const TRADE_NAME = 'trade_name';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
    ];

    public $timestamps = true;

    public static array $columns = [
        self::ID,
        self::CODE,
        self::BRANCH,
        self::CNPJ,
        self::DESCRIPTION,
        self::LEGAL_NAME,
        self::TRADE_NAME,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected function cnpj(): Attribute
    {
        return Attribute::make(
            get: static fn ($value) => preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", '$1.$2.$3/$4-$5', $value),
            set: static fn ($value) => preg_replace("/\D/", '', $value),
        );
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            CompanyUser::TABLE_NAME,
            CompanyUser::COMPANY_ID,
            CompanyUser::USER_ID,
        );
    }
}
