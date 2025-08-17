<?php

namespace App\Http\Controllers;

use App\Exceptions\WarningException;
use App\Helpers\Http\Responder;
use App\Http\Requests\Auth\AccountSetupFinaliseRequest;
use App\Http\Requests\ImageUploadRequest;
use App\Http\Requests\Settings\ChangePasswordRequest;
use App\Http\Requests\User\NotificationSettingChangeRequest;
use App\Http\Requests\User\ProfileUpdateRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly Responder   $responder,
    )
    {
    }

    public function info(): JsonResponse
    {
        return $this->responder->success(
            data: Auth::user()->intoShareable(),
            message: 'User info fetched successfully'
        );
    }

    /**
     * @param ImageUploadRequest $request
     * @return JsonResponse
     * @throws WarningException
     */
    public function uploadProfilePicture(ImageUploadRequest $request): JsonResponse
    {
        $user = $this->userService->uploadProfilePicture(Auth::id());

        return $this->responder->success(
            data: $user,
            message: 'Profile picture uploaded successfully'
        );
    }

    /**
     * @param ProfileUpdateRequest $request
     * @return JsonResponse
     */
    public function update(ProfileUpdateRequest $request): JsonResponse
    {
        $fullName = $request->validated('full_name');
        [
            'first_name' => $firstName,
            'last_name' => $lastName
        ] = UserService::FirstLastNameFromFullName($fullName);

        $this->userService->update(
            id: Auth::id(),
            firstName: $firstName,
            lastName: $lastName,
            email: $request->validated('email'),
            mobileNumber: null,
        );

        return $this->responder->successMessage('Profile updated successfully');
    }

    public function finaliseAccountSetup(AccountSetupFinaliseRequest $request)
    {
        $user = $this->userService->setupAccount(
            userId: Auth::id(),
            businessStageId: $request->validated('business_stage_id'),
            industryIds: $request->validated('industry_ids'),
            opportunityTypeIds: $request->validated('opportunity_type_ids'),
        );

        return $this->responder->success(
            data: $user,
            message: 'Account setup completed successfully'
        );
    }

    /**
     * @throws WarningException
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->userService->changePassword(
            userId: Auth::id(),
            old: $request->validated('old_password'),
            new: $request->validated('password'),
        );

        return $this->responder->successMessage('Password changed successfully');
    }

    public function changeNotificationSettings(NotificationSettingChangeRequest $request): JsonResponse
    {
        $user = $this->userService->changeNotificationSettings(
            user: Auth::user(),
            isNewsNotificationEnabled: $request->validated('is_news_notification_enabled'),
            isNewOppNotificationEnabled: $request->validated('is_new_opportunity_notification_enabled'),
            isAppOppNotificationEnabled: $request->validated('is_app_opportunity_notification_enabled'),
        );

        return $this->responder->success(
            data: $user,
            message: 'Notification settings changed successfully'
        );
    }
}
