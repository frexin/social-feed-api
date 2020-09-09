<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\User;
use DCN\RBAC\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller {

    /**
     * @param User $user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user) {
        return response()->json($this->formatUserResponse($user));
    }

    /**
     *
     */
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|max:255',
            'password' => 'required|min:6|max:255',
            'email' => 'required|email|unique:users',
            'photo' => 'file|image|max:2048'
        ]);
        $role = Role::where(['slug' => 'user'])->firstOrFail();
        $user = User::create(request()->all());
        $user->attachRole($role);

        $this->updatePhoto($user);

        return response()->json($this->formatUserResponse($user));
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user) {
        $this->validate($request, [
            'name' => 'max:255',
            'password' => 'min:6|max:255'
        ]);

        $params = request()->only(['email', 'password', 'name', 'selectedCategories']);
        $user->update($params);

        return response()->json($this->formatUserResponse($user));
    }

    /**
     * @param User $user
     * @return bool
     */
    private function updatePhoto(User $user) {
        if (request()->file('photo')) {
            $fileName = public_path(implode('/', [
                'img',
                'user',
                $user->id,
                request()->file('photo')->getBasename()
            ]));
            request()->file('photo')->move($fileName);
            $user->photo = $fileName;
            return $user->save();
        }
        return false;
    }
}
