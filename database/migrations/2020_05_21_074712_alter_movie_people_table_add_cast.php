<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMoviePeopleTableAddCast extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('movie_people', function (Blueprint $table) {
        $table->string('cast_type', 10)->after('role')->nullable();
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('movie_people', function (Blueprint $table) {
        $table->dropColumn('cast_type');
      });
    }
}
