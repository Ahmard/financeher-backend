<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAuthTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authToken = $request->bearerToken();
        $authToken ??= $request->get('token');
        $request->headers->set('Authorization', "Bearer $authToken");
        /**@phpstan-ignore-next-line **/
        auth()->setRequest($request);
        /**@phpstan-ignore-next-line **/
        auth()->guard('api')->setRequest($request);

        return $next($request);
    }
}
