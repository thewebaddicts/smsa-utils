<?php

namespace twa\smsautils\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use twa\apiutils\Traits\APITrait;

class AuthMandatoryMiddleware
{
    use APITrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next): Response
    {
        $access_token = request()->header('Access-Token');

        if (!$access_token) {
            $access_token = request()->cookie('Access-Token');
        }

        if (!$access_token) {
            return $this->response(notification()->error("Access Token is required", "Access Token is required"));
        }

        $access_token = DB::table('access_tokens')->where('token', $access_token)
            ->where('expires_at', '>', now())
            ->whereNull('deleted_at')
            ->first();

        if (!$access_token) {
            return $this->response(notification()->error("Access Token has expired or is invalid", "Access Token has expired or is invalid", 100));
        }


        switch ($access_token->tokenable_type) {
            case 'operator':


                $user = DB::table('operators')->whereId($access_token->tokenable_id)->first();
                $performer = $user->id . ' | ' . $user->name;

                break;

            case 'client_api_key':


                $user = DB::table('client_api_keys')->whereId($access_token->tokenable_id)->first();
                $performer = $user->id . ' | ' . $user->label;

                break;

            case 'user':

                $user = DB::table('users')->whereId($access_token->tokenable_id)->first();
                $performer = $user->id . ' | ' . $user->first_name . ' ' . $user->last_name;

                break;
        }

        request()->merge([
            'user' => $user,
            'user_type' => $access_token->tokenable_type,
            'user_performer' => $performer
        ]);

        return $next($request);
    }
}