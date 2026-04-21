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
            
     
        $username = env('INTERNAL_API_BASIC_USER');
        $password = env('INTERNAL_API_BASIC_PASS');

        $header = $request->header('Authorization');

        if (!$header || !str_starts_with($header, 'Basic ')) {
            return $this->response(notification()->error("Unauthorized", "Unauthorized") , 401);
        }

        $encoded = substr($header, 6);
        
        [$user, $pass] = explode(':', base64_decode($encoded), 2);

        if ($user !== $username || $pass !== $password) {
              return $this->response(notification()->error("Unauthorized", "Unauthorized") , 401);
        }

        return $next($request);
    

    }
}
