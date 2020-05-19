<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('title');
          $table->text('description');
          $table->string('poster_url',150);
          $table->integer('api_movie_id');
          $table->string('popularity',25);
          $table->timestamp('release_date');
          $table->string('vote_average',10);
          $table->unsignedMediumInteger('runtime');
          $table->timestamps();
          $table->index('title');
          $table->index('release_date');
          $table->softDeletes();
          $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movies');
    }
}
