<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FavUniIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->unique(['post_id', 'user_id'], 'unifav');
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->unique(['post_id', 'user_id'], 'unilike');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropUnique('unifav');
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->dropUnique('unilike');
        });
    }
}
