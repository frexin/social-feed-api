<?php

namespace App\Http\Middleware;

use App\Models\AccessToken;
use Closure;

class ApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');
        if ($token && $tokenModel = AccessToken::whereToken($token)->first()) {
            if (!$tokenModel->expired()) {
                \Auth::onceUsingId($tokenModel->user->id);
                $tokenModel->save();
            }
        }
        return $next($request);
    }
}
