<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use DCN\RBAC\Traits\HasRoleAndPermission;
use DCN\RBAC\Contracts\HasRoleAndPermission as HasRoleAndPermissionContract;
use DB;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements HasRoleAndPermissionContract {
    use HasRoleAndPermission;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
        //ulogin
        'network',
        'uid',
        'first_name',
        'last_name',
        'profile',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function token() {
        return $this->hasOne(AccessToken::class);
    }


    public function categories() {
        return $this->belongsToMany('App\Models\Category', 'user_categories', 'user_id', 'category_id');
    }


    public function getRoleNameAttribute() {
        $result = null;

        if ($roles = $this->getRoles() && isset($roles[0])) {
            $result = $roles[0]->name;
        }

        return $result;
    }

    /**
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function createUserIfNotExistsFrom(array $data) {
        $user = static::where('network', $data['network'])->where('uid', $data['uid'])->first();

        DB::transaction(function () use (&$user, $data) {
            if (!$user) {
                $user = new static($data);
                $user->name = static::generateNameFrom($data);
                $user->save();
                $user->attachRole(\DCN\RBAC\Models\Role::query()->where(['slug' => 'user'])->first());

                $user->prefillCategories();
                $user->token()->updateOrCreate([], ['token' => AccessToken::generate()]);
            }
            else {
                if (!$user->token) {
                    $user->token()->updateOrCreate([], ['token' => AccessToken::generate()]);
                } else {
                    $token = $user->token;
                    $token->updateExpiredAt();
                    $token->save();
                }
            }
        });

        return $user;
    }

    /**
     * @param array $data
     * @return string
     */
    public static function generateNameFrom(array $data) {
        $firstName = trim(array_get($data, 'first_name', ''));
        $lastName = trim(array_get($data, 'last_name', ''));
        $uid = trim($data['uid']);

        $name = $firstName;
        if (strlen($name) > 0) {
            if (strlen($lastName) > 0) {
                $name .= ' ' . $lastName;
            }
        } else {
            $name = $uid;
        }
        return $name;
    }

    public function prefillCategories() {
        foreach (Category::all() as $category) {
            $this->categories()->attach($category->id);
        }
    }

    public function update(array $attributes = [], array $options = []) {
        if (isset($attributes['password'])) {
            $attributes['password'] = \Hash::make($attributes['password']);
        }

        if (isset($attributes['selectedCategories'])) {
            $this->categories()->sync($attributes['selectedCategories']);

            unset($attributes['selectedCategories']);
        }

        return parent::update($attributes, $options);
    }
}
