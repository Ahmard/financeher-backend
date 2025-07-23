<?php

namespace App\Http\Controllers\Authentication;

use App\Exceptions\UnauthorizedException;
use App\Exceptions\WarningException;
use App\Helpers\Http\Responder;
use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginPostRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct(
        private readonly Responder   $responder,
        private readonly AuthService $authService,
    ) {
    }

    /**
     * @param LoginPostRequest $request
     * @return JsonResponse
     * @throws WarningException
     */
    public function login(LoginPostRequest $request): JsonResponse
    {
        $access = $this->authService->login(
            email: $request->validated('email'),
            password: $request->validated('password'),
        );

        return $this->responder->success(
            data: $access,
            message: 'Login successful'
        );
    }

    /**
     * @return JsonResponse
     * @throws BindingResolutionException
     * @throws UnauthorizedException
     */
    public function me(): JsonResponse
    {
        /** @var User|Model|null $user */
        $user = Auth::user();

        if (empty($user)) {
            throw new UnauthorizedException('You are not authenticated');
        }

        return $this->responder->success(
            data: $user->intoShareable(),
            message: 'Authenticated user info acquired'
        );
    }
}
