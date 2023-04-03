<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int               $id
 * @property string|null       $company_code_branch
 * @property string            $code
 * @property string            $store
 * @property string            $name
 * @property string            $business_name
 * @property string            $address
 * @property string|null       $number
 * @property string            $neighborhood
 * @property string            $state_code
 * @property string|null       $state_name
 * @property string            $postal_code
 * @property string            $cnpj_cpf
 * @property string            $phone_code
 * @property string            $phone_number
 * @property string            $contact
 * @property string            $email
 * @property Carbon|null       $created_at
 * @property Carbon|null       $updated_at
 * @property-read Company|null $company
 */
class Supplier extends Model
{
    public const TABLE_NAME = 'suppliers';
    public const ID = 'id';
    public const COMPANY_CODE_BRANCH = 'company_code_branch';
    public const CODE = 'code';
    public const STORE = 'store';
    public const NAME = 'name';
    public const BUSINESS_NAME = 'business_name';
    public const ADDRESS = 'address';
    public const NUMBER = 'number';
    public const NEIGHBORHOOD = 'neighborhood';
    public const STATE_CODE = 'state_code';
    public const STATE_NAME = 'state_name';
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

    protected $table = self::TABLE_NAME;
    protected $guarded = [
        self::ID,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(
            Company::class,
            self::COMPANY_CODE_BRANCH,
            Company::CODE_BRANCH
        );
    }

    protected function cnpjCpf(): Attribute
    {
        return Attribute::make(
            get: static fn ($value) => match (strlen($value)) {
                11 => preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", '$1.$2.$3-$4', $value),
                14 => preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", '$1.$2.$3/$4-$5', $value),
                default => $value,
            },
            set: static fn ($value) => preg_replace("/\D/", '', $value),
        );
    }
}
