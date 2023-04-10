<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int                    $id
 * @property string                 $code
 * @property string                 $code_branch
 * @property string                 $branch
 * @property string                 $name
 * @property string                 $business_name
 * @property string|null            $phone_number
 * @property string|null            $fax_number
 * @property string                 $cnpj_cpf
 * @property string|null            $state_inscription
 * @property string|null            $inscm
 * @property string|null            $address
 * @property string|null            $complement
 * @property string|null            $neighborhood
 * @property string|null            $city
 * @property string|null            $state
 * @property string|null            $postal_code
 * @property string|null            $city_code
 * @property string|null            $cnae
 * @property Carbon|null            $created_at
 * @property Carbon|null            $updated_at
 * // Virtual
 * @property-read string            $codeBranchAndBranch
 * // Relations
 * @property-read User[]|Collection $users
 */
class Company extends Model
{
    use HasFactory;

    public const TABLE_NAME = 'companies';
    public const ID = 'id';
    public const CODE = 'code';
    public const CODE_BRANCH = 'code_branch';
    public const BRANCH = 'branch';
    public const NAME = 'name';
    public const BUSINESS_NAME = 'business_name';
    public const PHONE_NUMBER = 'phone_number';
    public const FAX_NUMBER = 'fax_number';
    public const CNPJ_CPF = 'cnpj_cpf';
    public const STATE_INSCRIPTION = 'state_inscription';
    public const INSCM = 'inscm';
    public const ADDRESS = 'address';
    public const COMPLEMENT = 'complement';
    public const NEIGHBORHOOD = 'neighborhood';
    public const CITY = 'city';
    public const STATE = 'state';
    public const POSTAL_CODE = 'postal_code';
    public const CITY_CODE = 'city_code';
    public const CNAE = 'cnae';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Virtual
    public const CODE_BRANCH_AND_BRANCH = 'codeBranchAndBranch';

    // Relations
    public const RELATION_USERS = 'users';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
    ];

    protected function codeBranchAndBranch(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => "{$attributes[self::CODE_BRANCH]} {$attributes[self::BRANCH]}"
        );
    }

    protected function cnpjCpf(): Attribute
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
