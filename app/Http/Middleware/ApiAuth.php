<?php

namespace App\Http\Middleware;

use App\Models\AccessToken;
use Closure;
use Illuminate\Contracts\Validation\UnauthorizedException;

class ApiAuth
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
        if (!$token) {
            throw new UnauthorizedException();
        }

        $token = AccessToken::whereToken($token)->first();

        if (!$token) {
            throw new UnauthorizedException('invalid token');
        }

        if ($token->expired()) {
            throw new UnauthorizedException('expired token');
        }

        \Auth::onceUsingId($token->user->id);
        $token->save(); //updates expired_at for 7 days

        return $next($request);
    }
}
