<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use JeffGreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int                       $id
 * @property string                    $name
 * @property string                    $email
 * @property Carbon|null               $email_verified_at
 * @property string                    $password
 * @property string|null               $two_factor_secret
 * @property string|null               $two_factor_recovery_codes
 * @property Carbon|null               $two_factor_confirmed_at
 * @property string|null               $remember_token
 * @property Carbon|null               $created_at
 * @property Carbon|null               $updated_at
 * @property-read Company[]|Collection $companies
 */
class User extends Authenticatable implements FilamentUser
{
    public const TABLE_NAME = 'users';
    public const ID = 'id';
    public const NAME = 'name';
    public const EMAIL = 'email';
    public const EMAIL_VERIFIED_AT = 'email_verified_at';
    public const PASSWORD = 'password';
    public const TWO_FACTOR_SECRET = 'two_factor_secret';
    public const TWO_FACTOR_RECOVERY_CODES = 'two_factor_recovery_codes';
    public const TWO_FACTOR_CONFIRMED_AT = 'two_factor_confirmed_at';
    public const REMEMBER_TOKEN = 'remember_token';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    use HasApiTokens, HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

    protected $guarded = [
        self::ID,
    ];

    protected $hidden = [
        self::PASSWORD,
        self::REMEMBER_TOKEN,
        self::TWO_FACTOR_SECRET,
    ];

    protected $casts = [
        self::EMAIL_VERIFIED_AT => 'datetime',
    ];

    public static array $columns = [
        self::ID,
        self::NAME,
        self::EMAIL,
        self::EMAIL_VERIFIED_AT,
        self::TWO_FACTOR_RECOVERY_CODES,
        self::TWO_FACTOR_CONFIRMED_AT,
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(config('filament-shield.super_admin.name'));
    }

    public function companies(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Company::class,
                CompanyUser::TABLE_NAME,
                CompanyUser::USER_ID,
                CompanyUser::COMPANY_ID
            );
    }

    public function canAccessFilament(): bool
    {
        return true;
    }
}
