<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->string('first_name')->nullable()->default(null);
            $table->string('last_name')->nullable()->default(null);
            $table->string('network')->nullable()->default(null);
            $table->string('uid')->nullable()->default(null);
            $table->string('profile')->nullable()->default(null);
            $table->unique(['network', 'uid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropUnique(['network', 'uid']);
            $table->dropColumn(['first_name', 'last_name', 'network', 'uid', 'profile']);
        });
    }
}
