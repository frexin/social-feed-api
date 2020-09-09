<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sources', function(Blueprint $table) {
            $table->dropColumn('depth');
            $table->integer('last_post_id', false, true)->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sources', function(Blueprint $table) {
            $table->dropColumn('last_post_id');
            $table->integer('depth');
        });
    }
}
