<?php

namespace App\Http\Middleware;

use App\Helpers\Http\Responder;
use App\Models\User;
use App\Repositories\UserSessionRepository;
use App\Services\AuthService;
use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

readonly class AuthMiddleware
{
    public function __construct(
        private UserSessionRepository $userSessionRepository,
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @param mixed ...$guards
     * @return Response
     * @throws BindingResolutionException
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $user = $request->user();

        if (!$user) {
            return (new Responder())->unauthorized('You are not authorized to access this resources');
        }

        /** @phpstan-ignore-next-line  **/
        $payload = auth()->payload();
        if (!$this->userSessionRepository->isJwtUsable($payload['jti'])) {
            return (new Responder())->unauthorized('Your session has expired. Please login again');
        }

        $cacheKey = User::makeCacheKey($user->id);
        $userPermissions = Cache::get($cacheKey);

        if (empty($userPermissions)) {
            $userPermissions = AuthService::new()->getUserPermissionNames($user->id);

            Cache::put($cacheKey, $userPermissions);
            Log::info("caching($cacheKey)...");
        }

        if (!empty($guards) && !in_array($guards[0], $userPermissions)) {
            return (new Responder())
                ->forbidden('You do not have sufficient permission to access requested resource(s)');
        }

        return $next($request);
    }
}
