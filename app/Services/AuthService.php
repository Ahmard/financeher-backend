<?php

namespace App\Services;

use App\Enums\Statuses\UserStatus;
use App\Enums\Types\LogTrailActionType;
use App\Enums\Types\LogTrailEntityType;
use App\Enums\UserRole;
use App\Exceptions\WarningException;
use App\Models\User;
use App\Repositories\ModelHasRoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class AuthService extends BaseService
{
    public function __construct(
        private readonly LoginService    $loginService,
        private readonly RegisterService $registerService,
        private readonly MailService     $mailService,
        private readonly UserService     $userService,
        private readonly LogTrailService $logTrailService,
    ) {
    }

    /**
     * @param string $email
     * @param string $password
     * @return array
     * @throws WarningException
     */
    public function login(string $email, string $password): array
    {
        return $this->loginService->login($email, $password);
    }

    /**
     * @param string $code
     * @return Model|User
     * @throws WarningException
     */
    public function verifyOnboardingCode(string $code): User|Model
    {
        $user = $this->userService->repository()->findByAccountVerificationCode($code);
        if ($user == null) {
            throw new WarningException('Invalid email verification token');
        }

        return $this->verifyUser($user);
    }

    /**
     * @param string $token
     * @return Model|User
     * @throws WarningException
     */
    public function verifyOnboardingToken(string $token): User|Model
    {
        $user = $this->userService->repository()->findByAccountVerificationToken($token);
        if ($user == null) {
            throw new WarningException('Invalid email verification token');
        }

        return $this->verifyUser($user);
    }

    /**
     * @param User|Model $user
     * @return Model|User
     */
    public function verifyUser(User|Model $user): User|Model
    {
        $user = $this->userService->markUserAsEmailVerified($user);

        $this->mailService
            ->setSubject(sprintf('Welcome to %s', config('app.name')))
            ->setRecipient($user['email'])
            ->view('mails.auth.welcome-email-verified', [
                'user' => $user,
                'helpCenterLink' => support_url(''),
                'dashboardLink' => frontend('')
            ])
            ->send();

        return $user;
    }

    /**
     * @param string|null $businessName
     * @param string $firstName
     * @param string|null $lastName
     * @param string $email
     * @param string $rawPassword
     * @param string|null $mobileNumber
     * @param string|null $profilePicture
     * @param UserStatus $status
     * @return User|Model
     * @throws WarningException
     */
    public function register(
        ?string    $businessName,
        string     $firstName,
        ?string    $lastName,
        string     $email,
        string     $rawPassword,
        ?string    $mobileNumber,
        ?string    $profilePicture = null,
        UserStatus $status = UserStatus::ACTIVE,
    ): User|Model {
        return $this->registerService->create(
            businessName: $businessName,
            firstName: $firstName,
            lastName: $lastName,
            email: $email,
            rawPassword: $rawPassword,
            mobileNumber: $mobileNumber,
            profilePicture: $profilePicture,
            status: $status,
        );
    }

    /**
     * @param User|Model $user
     * @param string $oldPassword
     * @param string $password
     * @return void
     * @throws WarningException
     */
    public function changePassword(
        User|Model $user,
        string     $oldPassword,
        string     $password
    ): void {
        if ($password == $oldPassword) {
            throw new WarningException('New password cannot be same as old password');
        }

        if (!Hash::check($oldPassword, $user['password'])) {
            throw new WarningException('Your old password is incorrect.');
        }

        $this->userService->setPassword(
            user: $user,
            rawPassword: $password
        );

        $this->logTrailService->create(
            userId: $user['id'],
            entityId: $user['id'],
            entityType: LogTrailEntityType::USER,
            action: LogTrailActionType::UPDATE,
            desc: 'user changed password'
        );

        $this->mailService
            ->setSubject('Changed Password')
            ->setRecipient($user['email'], $user->fullName())
            ->view('mails.auth.password-reset-success', [
                'user' => $user,
                'login_url' => frontend('login')
            ])
            ->send();
    }

    /**
     * @param int|User $user
     * @return string[]
     * @throws BindingResolutionException
     */
    public function getUserRoles(int|User $user): array
    {
        $user = $this->getUser($user);
        $userRoles = ModelHasRoleRepository::new()->getUserRoles($user['id']);
        return $userRoles->toArray();
    }

    /**
     * @param int|User|Authenticatable $user
     * @return string[]
     * @throws BindingResolutionException
     */
    public function getUserRoleNames(int|User|Authenticatable $user): array
    {
        $user = $this->getUser($user);
        return $user->getRoleNames()->toArray();
    }

    public function hasRole(int|User|Authenticatable $user, UserRole $role): bool
    {
        $user = $this->getUser($user);
        return $user->hasRole($role->lowercase());
    }

    /**
     * @param int|User $user
     * @return string[]
     * @throws BindingResolutionException
     */
    public function getUserPermissions(int|User $user): array
    {
        $user = $this->getUser($user);
        return $user->getAllPermissions()->toArray();
    }

    /**
     * @param int|User|Authenticatable $user
     * @return string[]
     * @throws BindingResolutionException
     */
    public function getUserPermissionNames(int|User|Authenticatable $user): array
    {
        $user = $this->getUser($user);
        return array_map(fn (array $p) => $p['name'], $user->getAllPermissions()->toArray());
    }

    public function getSystemAccount(): Model|User
    {
        return $this
            ->userService
            ->repository()
            ->findRequiredById(UserService::SYSTEM_ACCOUNT_ID);
    }

    /**
     * @param int|User $user
     * @return Model|User
     * @throws BindingResolutionException
     */
    protected function getUser(int|User $user): Model|User
    {
        if (is_int($user)) {
            return UserRepository::new()->findRequiredById($user);
        }

        return $user;
    }
}
