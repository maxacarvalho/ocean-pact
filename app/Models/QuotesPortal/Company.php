<?php

namespace App\Models\QuotesPortal;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int                        $id
 * @property string                     $code
 * @property string                     $code_branch
 * @property string                     $branch
 * @property string                     $name
 * @property-read string                $name_and_branch
 * @property string                     $business_name
 * @property string|null                $phone_number
 * @property string|null                $fax_number
 * @property string                     $cnpj_cpf
 * @property string|null                $state_inscription
 * @property string|null                $inscm
 * @property string|null                $address
 * @property string|null                $complement
 * @property string|null                $neighborhood
 * @property string|null                $city
 * @property string|null                $state
 * @property string|null                $postal_code
 * @property string|null                $city_code
 * @property string|null                $cnae
 * @property Carbon|null                $created_at
 * @property Carbon|null                $updated_at
 * @property-read string                $code_code_branch_and_business_name
 * @property-read string                $code_code_branch_and_branch
 * Virtual
 * @property-read string                $codeBranchAndBranch
 * Relations
 * @property-read User[]|Collection     $users
 * @property-read Supplier[]|Collection $suppliers
 */
class Company extends Model
{
    public const TABLE_NAME = 'companies';
    public const ID = 'id';
    public const CODE = 'code';
    public const CODE_BRANCH = 'code_branch';
    public const BRANCH = 'branch';
    public const NAME_AND_BRANCH = 'name_and_branch';
    public const NAME = 'name';
    public const CODE_CODE_BRANCH_AND_BUSINESS_NAME = 'code_code_branch_and_business_name';
    public const CODE_CODE_BRANCH_AND_BRANCH = 'code_code_branch_and_branch';
    public const CODE_AND_BUSINESS_NAME = 'code_and_business_name';
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
    public const RELATION_SUPPLIERS = 'suppliers';

    protected $table = self::TABLE_NAME;

    protected $guarded = [
        self::ID,
        self::CODE_CODE_BRANCH_AND_BUSINESS_NAME,
        self::CODE_CODE_BRANCH_AND_BRANCH,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    protected function codeBranchAndBranch(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => "{$attributes[self::BUSINESS_NAME]} - {$attributes[self::BRANCH]}"
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

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(
            Supplier::class,
            CompanySupplier::TABLE_NAME,
            CompanySupplier::COMPANY_ID,
            CompanySupplier::SUPPLIER_ID,
        );
    }
}
