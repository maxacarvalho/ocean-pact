<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use JeffGreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property Carbon|null $email_verified_at
 * @property string      $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class User extends Authenticatable
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

    public const DELETED_AT = 'deleted_at';

    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

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
        self::DELETED_AT,
    ];
}
