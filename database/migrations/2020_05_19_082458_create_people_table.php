<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('name',100);
          $table->text('description');
          $table->string('image_url',100);
          $table->tinyInteger('gender');
          $table->string('popularity', 25);
          $table->integer('api_id');
          $table->timestamp('birth_day');
          $table->timestamp('death_day');
          $table->timestamps();
          $table->index('name');
          $table->index('birth_day');
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
        Schema::dropIfExists('people');
    }
}
