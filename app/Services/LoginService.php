<?php

namespace App\Services;

use App\Enums\Statuses\UserStatus;
use App\Exceptions\WarningException;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class LoginService extends BaseService
{
    public function __construct(
        private readonly UserService        $userService,
        private readonly UserSessionService $sessionService,
    )
    {
    }

    /**
     * @param string $email
     * @param string $password
     * @return array uuid for the generated otp for verification
     * @throws WarningException
     */
    public function login(string $email, string $password): array
    {
        $user = $this->userService->repository()->findByEmail($email);
        if (null == $user) {
            throw new WarningException('Invalid email address or password');
        }

        if ($user['has_started_password_reset']) {
            throw new WarningException('You started password reset, please follow the link sent to your email to complete it');
        }

        if (UserStatus::PENDING->nameIs($user['status'])) {
            throw new WarningException('Your account has not been activated yet!');
        }

        if (UserStatus::INACTIVE->nameIs($user['status'])) {
            throw new WarningException($user['system_message'] ?? 'Your account has been suspended, contact support for assistance');
        }

        if (!Hash::check($password, $user['password'])) {
            $user = $this->userService->trackFailedLogin($user['id']);
            if ($user['failed_logins'] >= 3) {
                $this->userService->deactivateUserDueToLoginTrials($user);
            }

            throw new WarningException('Invalid email address or password');
        }

        return $this->logUserIn($user);
    }

    /**
     * @return array{access_token: string, token_type: string, expires_in: int, expires_at: int}
     */
    public function logUserIn(User|Model $user): array
    {
        /**
         * @var string $token
         * @phpstan-ignore-next-line
         */
        $token = auth()->login($user);

        $this->userService->updateLastLogin($user);

        /** @phpstan-ignore-next-line  * */
        $payload = auth()->payload();
        $this->sessionService->create(
            userId: $user['id'],
            jti: $payload['jti']
        );

        /** @phpstan-ignore-next-line */
        $expiresIn = intval(auth()->factory()->getTTL()) * 60;

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => intval($expiresIn),
            'expires_at' => $expiresIn + time(),
        ];
    }
}