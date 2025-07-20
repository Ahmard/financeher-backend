<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use App\Services\AuthService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Nicolaslopezj\Searchable\SearchableTrait;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property Wallet $wallet
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    use HasRoles {
        hasRole as has_role;
    }

    use HasPermissions;
    use SearchableTrait;

    protected array $searchable = [
        'columns' => [
            'users.first_name' => 10,
            'users.last_name' => 10,
            'users.email' => 10,
            'users.mobile_number' => 10,
        ],
    ];

    protected $fillable = [];
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'deleted_at',
        'password',
        'remember_token',
        'login_otp',
        'login_otp_expires_at',
        'email_verification_token',
        'email_verified_at',
        'has_password',
        'is_password_locked',
        'has_started_password_reset',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_password_reset_at' => 'datetime',
            'password' => 'hashed',
            'has_password' => 'boolean',
            'is_password_locked' => 'boolean',
            'has_started_password_reset' => 'boolean',
        ];
    }
    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public static function makeCacheKey(int $id): string
    {
        return "user-{$id}-permissions";
    }

    public static function getFullNameColumn(
        string  $table = 'users',
        ?string $prefix = null,
        string  $as = 'full_name'
    ): Expression
    {
        $columns = self::getDatatableFilterFullNameColumn(
            table: $table,
            prefix: $prefix,
            searchable: false
        );

        return DB::raw("$columns AS $as");
    }

    public static function getDatatableFilterFullNameColumn(
        string  $table = 'users',
        ?string $prefix = '',
        bool    $searchable = true
    ): string
    {
        $sql = match (empty($prefix)) {
            true => "TRIM(CONCAT($table.first_name, ' ', $table.last_name))",
            false => "TRIM(CONCAT($table.{$prefix}_first_name, ' ', $table.{$prefix}_last_name))"
        };

        if ($searchable) {
            return "$sql LIKE ?";
        }

        return $sql;
    }

    public function getNotFoundMessage(): string
    {
        return "Such user does not exists";
    }

    public function fullName(): string
    {
        return sprintf('%s %s', $this['first_name'], $this['last_name']);
    }

    public function isAdmin(): bool
    {
        return $this->has_role([
            UserRole::ADMIN->name,
            UserRole::SUPER_ADMIN->name,
        ]);
    }

    public function isSuperAdmin(): bool
    {
        return $this->has_role(UserRole::SUPER_ADMIN->name);
    }

    /**
     * @throws BindingResolutionException
     */
    public function intoShareable(): array
    {
        return [
            'id' => $this['id'],
            'username' => $this['username'],
            'first_name' => $this['first_name'],
            'last_name' => $this['last_name'],
            'full_name' => $this['first_name'] . ' ' . $this['last_name'],
            'email' => $this['email'],
            'mobile_number' => $this['mobile_number'],
            'profile_picture' => $this['profile_picture'],
            'status' => $this['status'],
            'created_at' => $this['created_at'],
            'updated_at' => $this['updated_at'],

            'auth_data' => [
                'role_names' => AuthService::new()->getUserRoleNames($this),
                'permission_names' => AuthService::new()->getUserPermissionNames($this)
            ]
        ];
    }
}
