<?php

namespace App\Services;

use App\Enums\Statuses\UserStatus;
use App\Exceptions\WarningException;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RegisterService extends BaseService
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    public function create(
        string     $countryId,
        array      $businessTypeIds,
        array      $businessStageIds,
        array      $opportunityTypeIds,
        ?string    $businessName,
        string     $firstName,
        string     $lastName,
        string     $email,
        string     $rawPassword,
        ?string    $mobileNumber,
        ?string    $profilePicture = null,
        UserStatus $status = UserStatus::ACTIVE,
    ): User|Model {
        $existingEmail = $this->userService->repository()->findByEmail($email);
        if (null != $existingEmail) {
            throw new WarningException('Account with provided email address already exists');
        }

        if ($mobileNumber) {
            $existingMobileNumber = $this->userService->repository()->findByMobileNumber($mobileNumber);
            if (null != $existingMobileNumber) {
                throw new WarningException('Account with provided mobile number already exists');
            }
        }

        return $this->userService->create(
            invitedBy: null,
            countryId: $countryId,
            businessTypeIds: $businessTypeIds,
            businessStageIds: $businessStageIds,
            opportunityTypeIds: $opportunityTypeIds,
            businessName: $businessName,
            firstName: $firstName,
            lastName: $lastName,
            email: $email,
            rawPassword: $rawPassword,
            mobileNumber: $mobileNumber,
            profilePicture: $profilePicture,
            withVerificationEmail: true,
            status: $status,
        );
    }
}
