<?php

namespace App\Repositories;

use App\Enums\Statuses\UserStatus;
use App\Enums\Types\UserRegistrationStage;
use App\Exceptions\ModelNotFoundException;
use App\Helpers\Carbon;
use App\Models\Permission;
use App\Models\User;
use App\QueryBuilders\UserQueryBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class UserRepository extends BaseRepository
{
    public function __construct(
        private readonly UserQueryBuilder $queryBuilder,
    )
    {
    }

    public function create(
        ?int       $invitedBy,
        ?string    $industryId,
        ?string    $businessName,
        string     $firstName,
        ?string    $lastName,
        string     $email,
        ?string    $password,
        ?string    $mobileNumber,
        string     $accountVerificationCode,
        string     $accountVerificationToken,
        bool       $isAdmin,
        ?string    $profilePicture = null,
        UserStatus $status = UserStatus::ACTIVE,
    ): User|Model
    {
        return User::query()->create([
            'invited_by' => $invitedBy,
            'industry_id' => $industryId,
            'business_name' => $businessName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'mobile_number' => $mobileNumber,
            'profile_picture' => $profilePicture,
            'email_verification_code' => $accountVerificationCode,
            'email_verification_token' => $accountVerificationToken,
            'password' => $password,
            'is_admin' => $isAdmin,
            'has_password' => !empty($password),
            'status' => $status->lowercase(),
            'registration_stage' => UserRegistrationStage::EMAIL_VERIFICATION->lowercase(),
        ]);
    }

    public function update(
        int     $id,
        string  $firstName,
        ?string $lastName,
        string  $email,
        string  $mobileNumber,
        ?string $profilePicture = null,
        ?string $nin = null,
    ): Model|User
    {
        $user = $this->findRequiredById($id);
        $user->update([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'mobile_number' => $mobileNumber,
            'profile_picture' => $profilePicture,
            'nin' => $nin,
        ]);
        return $user;
    }

    public function changeStatus(int $id, UserStatus $status): Model|User
    {
        $user = $this->findRequiredById($id);
        $user->update(['status' => $status->lowercase()]);
        return $user;
    }

    public function findRequiredByEmail(string $email, ?UserStatus $status = null): Model|User
    {
        $user = $this->findByEmail($email, status: $status);

        if (null == $user) {
            throw new ModelNotFoundException('Such user does not exists');
        }

        return $user;
    }

    public function isAccountVerificationTokenValid(string $token): bool
    {
        return User::query()
            ->where('email_verification_token', $token)
            ->exists();
    }

    public function findByAccountVerificationCode(string $token): Model|User|null
    {
        return User::query()
            ->where('email_verification_code', $token)
            ->first();
    }

    public function findByAccountVerificationToken(string $token): Model|User|null
    {
        return User::query()
            ->where('email_verification_token', $token)
            ->first();
    }

    public function findByEmail(string $email, ?array $columns = null, ?UserStatus $status = null): Model|User|null
    {
        $builder = $this->queryBuilder;
        if ($status) {
            $builder->withStatus($status);
        }
        if ($columns) {
            $builder->withSelect($columns);
        }
        return $builder->findByEmail($email)->first();
    }

    public function findByMobileNumber(string $number, ?array $columns = null, ?UserStatus $status = null): Model|User|null
    {
        $builder = $this->queryBuilder;
        if ($status) {
            $builder->withStatus($status);
        }
        if ($columns) {
            $builder->withSelect($columns);
        }
        return $builder->findByMobileNumber($number)->first();
    }

    public function fetchFullName(int $userId): string
    {
        $fullName = User::query()
            ->selectRaw('CONCAT(first_name, \' \', last_name) as full_name')
            ->where('users.id', $userId)
            ->value('full_name');

        if (null == $fullName) {
            throw new ModelNotFoundException('This user does not exists');
        }

        return $fullName;
    }

    /**
     * @param int $id
     * @return Collection<int, Role>
     */
    public function roles(int $id): Collection
    {
        $user = $this->findRequiredById($id);
        /** @phpstan-ignore-next-line */
        return $user->roles;
    }

    /**
     * @param int $id
     * @return Collection<int, Permission>
     */
    public function permissions(int $id): Collection
    {
        $user = $this->findRequiredById($id);
        return $user->getAllPermissions();
    }

    public function getBasicInfo(int $userId): User|Model
    {
        $user = User::query()
            ->select([User::getFullNameColumn(), 'email', 'mobile_number', 'id'])
            ->where('users.id', $userId)
            ->first();

        if (null == $user) {
            Log::debug(sprintf('user with id(%d) does not exists', $userId));
            throw new ModelNotFoundException('This user does not exists');
        }

        return $user;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersThatCanBeActivated(): Collection
    {
        return User::query()
            ->where('users.status', UserStatus::INACTIVE->lowercase())
            ->where('users.suspended_until', '<=', Carbon::now())
            ->get();
    }

    public function queryBuilder(): UserQueryBuilder
    {
        return $this->queryBuilder;
    }
}
