<?php

namespace twa\smsautils\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use twa\apiutils\Traits\APITrait;

class BasicAuthMiddleware
{

    use APITrait;
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $username = (string) config('smsa-utils.internal_api_basic_user', '');
        $password = (string) config('smsa-utils.internal_api_basic_pass', '');

        if ($username === '' || $password === '') {
            return $this->response(notification()->error("Unauthorized", "Unauthorized"), 401);
        }

        $user = $request->getUser();
        $pass = $request->getPassword();

        if (!is_string($user) || !is_string($pass)) {
            return $this->response(notification()->error("Unauthorized", "Unauthorized"), 401);
        }

        if (!hash_equals($username, $user) || !hash_equals($password, $pass)) {
            return $this->response(notification()->error("Unauthorized", "Unauthorized"), 401);
        }

        return $next($request);
    }
}
