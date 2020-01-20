<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('name', 100);
          $table->string('cif', 20);
          $table->string('email', 255)->unique();
          $table->string('password', 255);
          $table->string('token', 255)->nullable();
          $table->string('sector', 100)->nullable();
          $table->string('description', 255)->nullable();
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
        Schema::dropIfExists('companies');
    }
}
