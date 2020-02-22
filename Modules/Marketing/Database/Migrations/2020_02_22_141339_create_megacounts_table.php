<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMegacountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('megacounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('serial_number',20)->unique();
            $table->string('ip_address',16)->unique();
            $table->string('name',100);
            $table->string('descr',250)->nullable();
            $table->integer('place_id')->unsigned();
            $table->foreign('place_id')->references('id')->on('places');
            $table->string('status',10)->default('OK');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('megacounts');
    }
}
