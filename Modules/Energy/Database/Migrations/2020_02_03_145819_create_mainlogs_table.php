<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMainlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mainlogs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ecounter_id')->unsigned();
            $table->foreign('ecounter_id')->references('id')->on('ecounters');
            $table->string('year',4);
            $table->string('month',2);
            $table->float('encount');
            $table->float('delta');
            $table->float('price');
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
        Schema::dropIfExists('mainlogs');
    }
}
