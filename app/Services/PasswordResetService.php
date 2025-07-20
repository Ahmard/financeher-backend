<?php

namespace App\Services;

use App\Exceptions\ModelNotFoundException;
use App\Exceptions\WarningException;
use App\Helpers\Carbon;
use App\Repositories\PasswordResetRepository;
use Ramsey\Uuid\Uuid;

class PasswordResetService extends BasePersistableService
{
    public function __construct(
        private readonly PasswordResetRepository $repository,
        private readonly UserService             $userService,
        private readonly MailService             $mailService,
    )
    {
    }

    public function create(string $email): void
    {
        $user = $this->userService->repository()->findByEmail($email);
        if (null == $user) {
            throw new WarningException('Invalid email address');
        }

        // ensure user can only reset password once a day
        $lastPasswordResetAt = $user['last_password_reset_at'];
        if ($lastPasswordResetAt) {
            if ($user['last_password_reset_at']->gt(Carbon::now()->subDay())) {
                throw new WarningException('You can only reset your password once a day');
            }
        }

        if ($this->repository->findByEmail($email)) {
            $this->repository->deleteByEmail($email);
        }

        $token = md5(Uuid::uuid4() . Uuid::uuid4());

        $lifetimeMins = config('auth.password_reset_token_lifetime');
        $this->repository->create(
            email: $email,
            token: $token,
            expiry: Carbon::now()->addMinutes(intval($lifetimeMins))
        );

        $this->mailService
            ->setSubject('Password Reset')
            ->setRecipient($user['email'], $user->fullName())
            ->view('mails.auth.password-reset', [
                'user' => $user,
                'link' => frontend("reset-password?route=$token")
            ])
            ->send();
    }

    /**
     * @param string $token
     * @param string $password
     * @return void
     * @throws WarningException
     * @throws ModelNotFoundException
     */
    public function resetPassword(string $token, string $password): void
    {
        $passwordReset = $this->repository->findByToken($token);
        if (null == $passwordReset) {
            throw new WarningException('Invalid password reset token');
        }

        if (Carbon::now()->utc()->greaterThan($passwordReset['expires_at'])) {
            throw new WarningException('Password reset token has expired');
        }

        $user = $this->userService->resetPassword($passwordReset['email'], $password);

        $this->repository->deleteByEmail($passwordReset['email']);

        $this->mailService
            ->setSubject('Changed Password')
            ->setRecipient($user['email'], $user->fullName())
            ->view('mails.auth.password-reset-success', [
                'user' => $user,
                'login_url' => frontend('login')
            ])
            ->send();
    }

    public function repository(): PasswordResetRepository
    {
        return $this->repository;
    }
}
