<?php

use App\Models\User;
use DCN\RBAC\Models\Role;
use Illuminate\Database\Seeder;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin     = User::create([
            'name'     => 'admin',
            'email'    => 'admin@ruvi.com',
            'photo'    => '/',
            'password' => bcrypt('admin'),
        ]);
        $moderator = User::create([
            'name'     => 'moderator',
            'email'    => 'moderator@ruvi.com',
            'photo'    => '/',
            'password' => bcrypt('moderator'),
        ]);
        $user      = User::create([
            'name'     => 'user',
            'email'    => 'user@ruvi.com',
            'photo'    => '/',
            'password' => bcrypt('user'),
        ]);

        $admin->attachRole(Role::create([
            'name'        => 'Admin',
            'slug'        => 'admin',
            'description' => '', // optional
            'parent_id'   => null, // optional, set to NULL by default
        ]));
        $moderator->attachRole(Role::create([
            'name' => 'Site Moderator',
            'slug' => 'site.moderator',
        ]));
        $user->attachRole(Role::create([
            'name' => 'user',
            'slug' => 'user',
        ]));
    }
}
