<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Advertising extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advert', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('enable');
            $table->integer('posts_count');
            $table->string('url');
        });

        Schema::drop('adverts');
        Schema::drop('banners');

        DB::connection()->getPdo()->exec('INSERT INTO advert (`enable`, `posts_count`, `url`) VALUES (1, 5, "http://placehold.it/720x50")');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
