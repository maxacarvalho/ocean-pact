<?php

namespace App\Models\QuotesPortal;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int                       $id
 * @property string                    $company_code
 * @property string|null               $company_code_branch
 * @property string                    $code
 * @property string                    $store
 * @property string                    $name
 * @property string                    $business_name
 * @property string|null               $address
 * @property string|null               $number
 * @property string|null               $state_code
 * @property string|null               $postal_code
 * @property string|null               $cnpj_cpf
 * @property string|null               $phone_code
 * @property string|null               $phone_number
 * @property string                    $contact
 * @property string                    $email
 * @property Carbon|null               $created_at
 * @property Carbon|null               $updated_at
 * Relations
 * @property-read Company|null         $company
 * @property-read Company[]|Collection $companies
 * @property-read User[]|Collection    $users
 * @property-read User[]|Collection    $sellers
 */
class Supplier extends Model
{
    public const TABLE_NAME = 'suppliers';
    public const ID = 'id';
    public const COMPANY_CODE = 'company_code';
    public const COMPANY_CODE_BRANCH = 'company_code_branch';
    public const CODE = 'code';
    public const STORE = 'store';
    public const NAME = 'name';
    public const BUSINESS_NAME = 'business_name';
    public const ADDRESS = 'address';
    public const NUMBER = 'number';
    public const STATE_CODE = 'state_code';
    public const POSTAL_CODE = 'postal_code';
    public const CNPJ_CPF = 'cnpj_cpf';
    public const PHONE_CODE = 'phone_code';
    public const PHONE_NUMBER = 'phone_number';
    public const CONTACT = 'contact';
    public const EMAIL = 'email';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    // Relations
    public const RELATION_COMPANY = 'company';
    public const RELATION_USERS = 'users';
    public const RELATION_COMPANIES = 'companies';
    public const RELATION_SELLERS = 'sellers';

    protected $table = self::TABLE_NAME;
    protected $guarded = [
        self::ID,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class,
            self::COMPANY_CODE,
            Company::CODE
        );
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(
            Company::class,
            CompanySupplier::TABLE_NAME,
            CompanySupplier::SUPPLIER_ID,
            CompanySupplier::COMPANY_ID
        );
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            related: User::class,
            table: SupplierUser::TABLE_NAME,
            foreignPivotKey: SupplierUser::SUPPLIER_ID,
            relatedPivotKey: SupplierUser::USER_ID,
        )->withPivot(SupplierUser::CODE);
    }

    public function sellers(): BelongsToMany
    {
        return $this->belongsToMany(
            related: User::class,
            table: SupplierUser::TABLE_NAME,
            foreignPivotKey: SupplierUser::SUPPLIER_ID,
            relatedPivotKey: SupplierUser::USER_ID,
        )->withPivot(SupplierUser::CODE);
    }

    protected function cnpjCpf(): Attribute
    {
        return Attribute::make(
            get: static function ($value) {
                if (empty($value)) {
                    return $value;
                }

                return match (strlen($value)) {
                    11 => preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", '$1.$2.$3-$4', $value),
                    14 => preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", '$1.$2.$3/$4-$5', $value),
                    default => $value,
                };
            },
            set: static fn ($value) => preg_replace("/\D/", '', $value),
        );
    }
}
