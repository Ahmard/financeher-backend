<?php

namespace App\Http\Controllers;

use App\Exceptions\WarningException;
use App\Helpers\Http\Responder;
use App\Http\Requests\Settings\ChangePasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function __construct(
        private readonly Responder   $responder,
        private readonly AuthService $authService,
    ) {
    }

    /**
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     * @throws WarningException
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->authService->changePassword(
            user: Auth::user(),
            oldPassword: $request->validated('old_password'),
            password: $request->validated('password'),
        );

        return $this->responder->successMessage('password changed successfully');
    }
}
