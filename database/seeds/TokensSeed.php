<?php

use App\Models\AccessToken;
use Illuminate\Database\Seeder;

class TokensSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AccessToken::create([
            'user_id' => 1,
            'token'   => 'test_token'
        ]);
    }
}
