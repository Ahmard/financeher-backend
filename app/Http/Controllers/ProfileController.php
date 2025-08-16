<?php

namespace App\Http\Controllers;

use App\Exceptions\WarningException;
use App\Helpers\Http\Responder;
use App\Http\Requests\Auth\AccountSetupFinaliseRequest;
use App\Http\Requests\ImageUploadRequest;
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
        $this->userService->update(
            id: Auth::id(),
            firstName: $request->validated('first_name'),
            lastName: $request->validated('last_name'),
            email: $request->validated('email'),
            mobileNumber: $request->validated('mobile_number'),
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
}
