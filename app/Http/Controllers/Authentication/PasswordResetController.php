<?php

namespace App\Http\Controllers\Authentication;

use App\Exceptions\ModelNotFoundException;
use App\Exceptions\WarningException;
use App\Helpers\Http\Responder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\EmailRequest;
use App\Http\Requests\Authentication\PasswordResetPostRequest;
use App\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;

class PasswordResetController extends Controller
{
    public function __construct(
        private readonly Responder            $responder,
        private readonly PasswordResetService $service,
    ) {
    }

    /**
     * @param EmailRequest $request
     * @return JsonResponse
     * @throws WarningException
     */
    public function initiate(EmailRequest $request): JsonResponse
    {
        $this->service->create($request->email());
        return $this->responder->successMessage('password reset link sent to email address, follow the link to reset your password');
    }

    /**
     * @param string $token
     * @return JsonResponse
     */
    public function canReset(string $token): JsonResponse
    {
        return match ($this->service->repository()->canReset($token)) {
            true => $this->responder->successMessage('password reset link is valid'),
            false => $this->responder->warningMessage('password reset link is invalid'),
        };
    }

    /**
     * @param string $token
     * @param PasswordResetPostRequest $request
     * @return JsonResponse
     * @throws WarningException
     * @throws ModelNotFoundException
     */
    public function resetPassword(string $token, PasswordResetPostRequest $request): JsonResponse
    {
        $this->service->resetPassword(
            $token,
            $request->validated('password')
        );

        return $this->responder->successMessage('password reset successfully');
    }
}
