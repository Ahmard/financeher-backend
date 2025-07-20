<?php

namespace App\Http\Controllers\Authentication;

use App\Exceptions\ModelNotFoundException;
use App\Exceptions\WarningException;
use App\Helpers\Http\Responder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\EmailRequest;
use App\Http\Requests\Authentication\PasswordRequest;
use App\Http\Requests\Authentication\RegisterPostRequest;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\LoginService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    public function __construct(
        private readonly Responder   $responder,
        private readonly AuthService $authService,
    )
    {
    }

    /**
     * @param RegisterPostRequest $request
     * @return JsonResponse
     * @throws WarningException
     */
    public function register(RegisterPostRequest $request): JsonResponse
    {
        $access = $this->authService->register(
            countryId: $request->validated('country_id'),
            businessTypeIds: $request->validated('business_type_ids'),
            businessStageIds: $request->validated('business_stage_ids'),
            opportunityTypeIds: $request->validated('opportunity_type_ids'),
            businessName: null,
            firstName: $request->validated('first_name'),
            lastName: $request->validated('last_name'),
            email: $request->validated('email'),
            rawPassword: $request->validated('password'),
            mobileNumber: $request->validated('mobile_number'),
        );

        return $this->responder->success(
            data: $access,
            message: 'Registration successful. Please check your email for account verification.'
        );
    }

    /**
     * @param string $token
     * @param PasswordRequest $request
     * @param LoginService $loginService
     * @return JsonResponse
     * @throws WarningException
     */
    public function verifyEmail(
        string          $token,
        PasswordRequest $request,
        LoginService    $loginService,
    ): JsonResponse
    {
        $user = $this->authService->verifyOnboardingToken(
            token: $token,
            password: $request->password()
        );

        $access = $loginService->logUserIn($user);

        return $this->responder->success(
            data: $access,
            message: 'Email verified and you are logged in.'
        );
    }

    /**
     * @param EmailRequest $request
     * @param UserService $service
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws WarningException
     */
    public function resendAccountVerificationEmail(EmailRequest $request, UserService $service): JsonResponse
    {
        $service->resendAccountVerificationEmail($request->email());
        return $this->responder->successMessage('Account verification email sent.');
    }

    public function verifyTokenValidity(string $token, UserRepository $userRepository): JsonResponse
    {
        return $this->responder->success(
            data: ['isValid' => $userRepository->isAccountVerificationTokenValid($token)],
            message: 'Account verification token validity checked.'
        );
    }
}
