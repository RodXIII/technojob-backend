<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->string('surname', 100);
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->string('token', 255)->nullable();
            $table->string('about', 255)->nullable();
            $table->string('education', 255)->nullable();
            $table->string('skills', 255)->nullable();
            $table->string('experience', 255)->nullable();
            $table->string('url_img', 255)->nullable();
            $table->timestamps();
            $table->bigInteger('city_id')->unsigned();
            $table->foreign('city_id')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workers');
    }
}
