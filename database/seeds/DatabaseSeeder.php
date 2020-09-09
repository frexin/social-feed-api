<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(vk_groups::class);
        $this->call(UserSeed::class);
        $this->call(TokensSeed::class);
    }
}
