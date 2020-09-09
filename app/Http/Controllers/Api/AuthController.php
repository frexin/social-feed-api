<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccessToken;
use App\Models\User;
use App\Services\ULogin;
use Auth;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

use App\Http\Requests;

class AuthController extends Controller
{
    public function token()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')], false, false)) {
            /** @var User $user */
            $user  = User::whereEmail(request('email'))->firstOrFail();
            $token = AccessToken::create([
                'user_id'    => $user->id,
                'token'      => str_random(32),
                'expired_at' => Carbon::now()->addDays(7)
            ]);

            return response()->json([
                'access_token' => $token->token,
                'user'         => $this->formatUserResponse($user)
            ]);
        }
        throw new AuthorizationException('Invalid credentials');
    }

    public function login(ULogin $ulogin) {
        $token = request('token');

        $data = $ulogin->getUserDataByToken($token);
        if (isset($data['error'])) {
            return $data;
        }
        $user = User::createUserIfNotExistsFrom($data);
        $user->load('token');

        return ['user' => $this->formatUserResponse($user), 'token' => $user->token->token];
    }

    public function loginByEmail() {
        $email    = request('email');
        $password = request('password');

        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = User::where('email', $email)->first();

            $user->token()->updateOrCreate([], ['token' => AccessToken::generate()]);
            return ['user' => $this->formatUserResponse($user), 'token' => $user->token->token];
        }
        else {
            return ['error' => 'Invalid credentionals'];
        }
    }
}
