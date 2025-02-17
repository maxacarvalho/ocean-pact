<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\QuotesPortal\Company;
use App\Models\QuotesPortal\CompanyUser;
use App\Models\QuotesPortal\Supplier;
use App\Models\QuotesPortal\SupplierUser;
use App\Utils\Str;
use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $buyer_code
 * @property int|null $supplier_id
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property bool $is_draft
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool $active
 *                        Relations
 * @property-read Company[]|Collection  $companies
 * @property-read Role[]|Collection     $roles
 * @property-read Supplier[]|Collection $suppliers
 * @property-read Supplier|null         $supplier
 * @property-read SupplierUser|null     $supplier_user
 */
class User extends Authenticatable implements FilamentUser
{
    public const TABLE_NAME = 'users';

    public const ID = 'id';

    public const NAME = 'name';

    public const EMAIL = 'email';

    public const BUYER_CODE = 'buyer_code';

    public const SUPPLIER_ID = 'supplier_id';

    public const EMAIL_VERIFIED_AT = 'email_verified_at';

    public const PASSWORD = 'password';

    public const TWO_FACTOR_SECRET = 'two_factor_secret';

    public const TWO_FACTOR_RECOVERY_CODES = 'two_factor_recovery_codes';

    public const TWO_FACTOR_CONFIRMED_AT = 'two_factor_confirmed_at';

    public const REMEMBER_TOKEN = 'remember_token';

    public const IS_DRAFT = 'is_draft';

    public const CREATED_AT = 'created_at';

    public const UPDATED_AT = 'updated_at';

    public const ACTIVE = 'active';

    // Relations
    public const RELATION_COMPANIES = 'companies';

    public const RELATION_ROLES = 'roles';

    public const RELATION_SUPPLIERS = 'suppliers';

    public const RELATION_SUPPLIER = 'supplier';

    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $guarded = [
        self::ID,
    ];

    protected $hidden = [
        self::PASSWORD,
        self::REMEMBER_TOKEN,
        self::TWO_FACTOR_SECRET,
    ];

    protected function casts(): array
    {
        return [
            self::EMAIL_VERIFIED_AT => 'datetime',
            self::IS_DRAFT => 'boolean',
            self::ACTIVE => 'boolean',
        ];
    }

    public function companies(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Company::class,
                CompanyUser::TABLE_NAME,
                CompanyUser::USER_ID,
                CompanyUser::COMPANY_ID
            )
            ->as('buyer_company')
            ->withPivot(CompanyUser::BUYER_CODE);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Supplier::class,
            table: SupplierUser::TABLE_NAME,
            foreignPivotKey: SupplierUser::USER_ID,
            relatedPivotKey: SupplierUser::SUPPLIER_ID,
        )->withPivot(SupplierUser::CODE);
    }

    /**
     * @return \Illuminate\Support\Collection|Company[]
     */
    public function getSellerCompanies(): \Illuminate\Support\Collection|array
    {
        return $this->suppliers()
            ->with('companies')
            ->get()
            ->pluck('companies')
            ->flatten();
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(config('filament-shield.super_admin.name'));
    }

    public function isNotSuperAdmin(): bool
    {
        return ! $this->isSuperAdmin();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(
            Role::ROLE_ADMIN
        );
    }

    public function isSeller(): bool
    {
        return $this->hasRole(
            Role::ROLE_SELLER
        );
    }

    public function isBuyer(): bool
    {
        return $this->hasRole(
            Role::ROLE_BUYER
        );
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function canImpersonate(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function canBeImpersonated(): bool
    {
        return $this->isNotSuperAdmin();
    }

    public function activateAccount(string $password): void
    {
        $this->password = $password;
        $this->setRememberToken(Str::random(60));
        $this->is_draft = false;
        $this->save();
    }
}
