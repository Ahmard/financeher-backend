<?php

namespace App\Services;

use App\Enums\Entity;
use App\Enums\Statuses\UserStatus;
use App\Enums\Types\LogTrailActionType;
use App\Enums\Types\LogTrailEntityType;
use App\Enums\Types\SystemMessageType;
use App\Enums\Types\UserRegistrationStage;
use App\Enums\UserRole;
use App\Exceptions\ModelNotFoundException;
use App\Exceptions\WarningException;
use App\Helpers\Carbon;
use App\Models\User;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\Traits\StatusManipulatorTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\ArrayShape;
use Ramsey\Uuid\Uuid;

class UserService extends BasePersistableService
{
    use StatusManipulatorTrait;

    public const int SUPER_ADMIN_ACCOUNT_ID = 1;
    public const int SYSTEM_ACCOUNT_ID = 2;

    public function __construct(
        private readonly UserRepository             $repository,
        private readonly RoleRepository             $roleRepository,
        private readonly LogTrailService            $logTrailService,
        private readonly FileUploadService          $fileUploadService,
        private readonly MailService                $mailService,
        private readonly WalletService              $walletService,
        private readonly UserIndustryService        $userIndustryService,
        private readonly UserBusinessStageService   $userBusinessStageService,
        private readonly UserOpportunityTypeService $userOpportunityTypeService,
    )
    {
    }

    public function setupAccount(
        int    $userId,
        string $businessStageId,
        array  $industryIds,
        array  $opportunityTypeIds,
    ): User|Model
    {
        $user = $this->repository->findRequiredById($userId);
        $user->update([
            'business_stage_id' => $businessStageId,
            'registration_stage' => UserRegistrationStage::COMPLETED->lowercase(),
        ]);

        foreach ($industryIds as $industryId) {
            $this->userIndustryService->create(
                createdBy: $userId,
                userId: $userId,
                industryId: $industryId,
            );
        }

        foreach ($opportunityTypeIds as $opportunityTypeId) {
            $this->userOpportunityTypeService->create(
                createdBy: $userId,
                userId: $userId,
                typeId: $opportunityTypeId,
            );
        }

        return $user;
    }

    public function create(
        ?int       $invitedBy,
        ?string    $industryId,
        string     $firstName,
        ?string    $lastName,
        string     $email,
        ?string    $rawPassword,
        ?string    $mobileNumber,
        bool       $isAdmin,
        ?string    $profilePicture = null,
        bool       $withVerificationEmail = false,
        UserRole   $role = UserRole::CUSTOMER,
        UserStatus $status = UserStatus::ACTIVE,
    ): User|Model
    {
        $token = md5(Uuid::uuid4() . Uuid::uuid4());
        $user = $this->repository->create(
            invitedBy: $invitedBy,
            industryId: $industryId,
            firstName: $firstName,
            lastName: $lastName,
            email: $email,
            password: $rawPassword ? Hash::make($rawPassword) : null,
            mobileNumber: $mobileNumber,
            accountVerificationCode: strval(mt_rand(100000, 999999)),
            accountVerificationToken: $token,
            isAdmin: $isAdmin,
            profilePicture: $profilePicture,
            status: $status,
        );

        $this->walletService->create($user['id']);

        $user->assignRole($role->lowercase());

        if ($withVerificationEmail) {
            $this->sendAccountVerificationEmail($user);
        }

        return $user;
    }

    public function sendAccountVerificationEmail(User|Model $user): void
    {
        $this->mailService
            ->setSubject(sprintf('Welcome to %s - Verify Your Email', config('app.name')))
            ->setRecipient($user['email'])
            ->view('mails.auth.welcome-onboarding', [
                'user' => $user,
                'companyName' => config('app.name'),
                'verificationLink' => frontend("auth/account-verification?token={$user['email_verification_token']}")
            ])
            ->send();
    }

    /**
     * @param string $email
     * @return Model|User
     * @throws ModelNotFoundException
     * @throws WarningException
     */
    public function resendAccountVerificationEmail(string $email): Model|User
    {
        $user = $this->repository->findRequiredByEmail($email);

        if (empty($user['email_verification_token'])) {
            throw new WarningException('User is already verified');
        }

        $this->sendAccountVerificationEmail($user);
        return $user;
    }

    public function update(
        int     $id,
        string  $firstName,
        string  $lastName,
        string  $email,
        ?string $mobileNumber,
        ?string $profilePicture = null,
    ): Model|User
    {
        return $this->repository->update(
            id: $id,
            firstName: $firstName,
            lastName: $lastName,
            email: $email,
            mobileNumber: $mobileNumber,
            profilePicture: $profilePicture,
        );
    }

    public function activate(
        int     $id,
        int     $activatedBy,
        string  $reason,
        ?string $ownerId = null,
    ): Model|User
    {
        $user = $this->repository->changeStatus(
            id: $id,
            status: UserStatus::ACTIVE
        );

        $this->logTrailService->create(
            userId: $activatedBy,
            entityId: $id,
            entityType: LogTrailEntityType::USER,
            action: LogTrailActionType::UPDATE,
            desc: 'activated user',
            data: $user,
            reason: $reason,
            entitySubType: $ownerId,
        );

        return $user;
    }

    public function deactivate(
        int     $id,
        int     $deactivatedBy,
        string  $reason,
        ?string $ownerId = null,
    ): Model|User
    {
        $user = $this->repository->changeStatus(
            id: $id,
            status: UserStatus::INACTIVE
        );

        $this->logTrailService->create(
            userId: $deactivatedBy,
            entityId: $id,
            entityType: LogTrailEntityType::USER,
            action: LogTrailActionType::UPDATE,
            desc: 'deactivated user',
            data: $user,
            reason: $reason,
            entitySubType: $ownerId,
        );

        return $user;
    }

    public function pageMetrics(): array
    {
        return [
            'all' => User::query()->count(),
            'active' => User::query()
                ->where('status', UserStatus::ACTIVE->lowercase())
                ->count(),
            'suspended' => User::query()
                ->where('status', UserStatus::INACTIVE->lowercase())
                ->count(),
        ];
    }

    public function customerPageMetrics(): array
    {
        return [
            'all' => User::query()
                ->where('users.is_admin', false)
                ->count(),
            'active' => User::query()
                ->where('users.is_admin', false)
                ->where('status', UserStatus::ACTIVE->lowercase())
                ->count(),
            'suspended' => User::query()
                ->where('users.is_admin', false)
                ->where('status', UserStatus::INACTIVE->lowercase())
                ->count(),
        ];
    }

    public static function clearCache(int $userId): void
    {
        $cacheKey = User::makeCacheKey($userId);
        Cache::forget($cacheKey);
        Artisan::call('cache:clear');
    }

    /**
     * @return Collection<int, User|Model>
     */
    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    /**
     * @param int $id
     * @param int $roleId
     * @return User|Model
     */
    public function assignRole(int $id, int $roleId): User|Model
    {
        $user = $this->repository->findRequiredById($id);
        $role = $this->roleRepository->findRequiredById($roleId);
        $user->assignRole($role);

        CacheService::clear();

        return $user;
    }

    /**
     * @param int $id
     * @param int $roleId
     * @return User|Model
     */
    public function unassignRole(int $id, int $roleId): User|Model
    {
        $user = $this->repository->findRequiredById($id);
        $role = $this->roleRepository->findRequiredById($roleId);
        $user->removeRole($role);

        CacheService::clear();

        return $user;
    }

    public function getUserAccountVerificationUrl(int $id): string
    {
        $user = $this->repository->findRequiredById($id);
        return frontend("account-verification?route={$user['email_verification_token']}");
    }

    public function setPassword(User|Model $user, string $rawPassword): Model|User
    {
        $user->update([
            'password' => Hash::make($rawPassword),
            'last_password_reset_at' => Carbon::now(),
        ]);

        return $user;
    }

    public function changeNotificationSettings(
        int|Model|User $user,
        bool           $isNewsNotificationEnabled,
        bool           $isNewOppNotificationEnabled,
        bool           $isAppOppNotificationEnabled,
    ): Model|User
    {
        $user = $this->getUser($user);
        $user->update([
            'is_news_notification_enabled' => $isNewsNotificationEnabled,
            'is_new_opportunity_notification_enabled' => $isNewOppNotificationEnabled,
            'is_app_opportunity_notification_enabled' => $isAppOppNotificationEnabled,
        ]);

        return $user;
    }

    public function markUserAsEmailVerified(User|Model $user): User|Model
    {
        $cols = [
            'email_verified_at' => Carbon::now(),
            'email_verification_token' => null,
            'registration_stage' => UserRegistrationStage::PLAN_SUBSCRIPTION->lowercase(),
        ];

        if ($user['is_admin_team_member']) {
            $cols['status'] = UserStatus::ACTIVE->lowercase();
        }

        $user->update($cols);

        return $user;
    }

    public function markAsPaymentMade(int $id): void
    {
        $user = $this->repository->findRequiredById($id);
        $user->update([
            'registration_stage' => UserRegistrationStage::ACCOUNT_SETUP->lowercase(),
        ]);
    }

    /**
     * @param string $email
     * @param string $password
     * @return Model|User
     * @throws ModelNotFoundException
     * @throws WarningException
     */
    public function resetPassword(string $email, string $password): Model|User
    {
        $user = $this->repository->findRequiredByEmail($email);

        if (Hash::check($password, $user['password'])) {
            throw new WarningException('New password cannot be same as old password');
        }

        return $this->setPassword($user, $password);
    }

    public function changePassword(int $userId, string $old, string $new): void
    {
        if ($old == $new) {
            throw new WarningException('New password cannot be same as old password');
        }

        $user = $this->repository->findRequiredById($userId);
        if (!Hash::check($old, $user['password'])) {
            throw new WarningException('Old password is incorrect');
        }

        $user->update(['password' => Hash::make($new)]);
    }

    /**
     * @param int $userId
     * @return Model|User
     * @throws WarningException
     */
    public function uploadProfilePicture(int $userId): Model|User
    {
        $user = $this->repository->findRequiredById($userId);
        $files = $this->fileUploadService->upload(
            userId: $userId,
            entity: Entity::USER,
            ownerId: $userId,
            fieldName: 'image',
            desc: 'profile picture'
        );

        $uploaded = $files[0];

        $user->update(['profile_picture' => $uploaded['file_path']]);


        $this->logTrailService->create(
            userId: $userId,
            entityId: $userId,
            entityType: LogTrailEntityType::USER,
            action: LogTrailActionType::UPDATE,
            desc: 'uploaded profile picture',
            data: $user,
        );

        return $user;
    }

    /**
     * @param string $id
     * @return Model|User
     */
    public function trackFailedLogin(string $id): Model|User
    {
        $user = $this->repository->findRequiredById($id);
        $user->increment('failed_logins');
        return $user;
    }

    /**
     * @param User|Model $user
     * @return void
     */
    public function deactivateUserDueToLoginTrials(User|Model $user): void
    {
        if ($user->isSuperAdmin()) {
            Log::warning('Super admin should not be deactivated due to login trials, skipping...');
            return;
        }

        $periodMins = config('auth.user_suspension_period_minutes');
        $user->update([
            'status' => UserStatus::INACTIVE->lowercase(),
            'suspended_until' => \App\Helpers\Carbon::now()->addMinutes(intval($periodMins))
        ]);

        $this->mailService
            ->setSubject('Account Deactivation Notice')
            ->setRecipient($user['email'])
            ->view('mails.auth.user-account-deactivated')
            ->send();

        $this->logTrailService->create(
            userId: $user['id'],
            entityId: $user['id'],
            entityType: LogTrailEntityType::USER,
            action: LogTrailActionType::UPDATE,
            desc: 'deactivated user',
            reason: 'repetitive failed login trials',
        );

        $this->captureSystemMessage(
            user: $user,
            type: SystemMessageType::ERROR,
            message: 'Your account has been suspended due to repetitive failed login trials, contact support for assistance',
        );
    }

    public function activateUserAfterLoginTrialSuspension(User|Model $user): void
    {
        $user->update([
            'failed_logins' => 0,
            'suspended_until' => null,
            'status' => UserStatus::ACTIVE->lowercase(),
        ]);
    }

    /**
     * @param User|Model $user
     * @param SystemMessageType $type
     * @param string $message
     * @return void
     */
    private function captureSystemMessage(User|Model $user, SystemMessageType $type, string $message): void
    {
        $user->update([
            'system_message' => $message,
            'system_message_type' => $type->lowercase()
        ]);

    }

    public function updateLastLogin(User|Model $user): void
    {
        $user->update(['last_login_at' => Carbon::now()]);
    }

    #[ArrayShape(['first_name' => "string", 'last_name' => "string"])]
    public static function FirstLastNameFromFullName(string $name): array
    {
        $expName = explode(' ', $name);
        $firstName = $expName[0];
        unset($expName[0]);
        $lastName = implode(' ', $expName);

        return [
            'first_name' => $firstName,
            'last_name' => $lastName
        ];
    }

    public function getUser(int|Model|User $user): Model|User
    {
        if (is_int($user)) {
            return $this->repository->findRequiredById($user);
        }

        return $user;
    }

    public function repository(): UserRepository
    {
        return $this->repository;
    }
}
