<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRentlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rentlogs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('renter_id')->unsigned();
            $table->foreign('renter_id')->references('id')->on('renters');
            $table->date('data');
            $table->tinyInteger('period1')->default(0);
            $table->tinyInteger('period2')->default(0);
            $table->tinyInteger('period3')->default(0);
            $table->tinyInteger('period4')->default(0);
            $table->tinyInteger('period5')->default(0);
            $table->tinyInteger('period6')->default(0);
            $table->tinyInteger('period7')->default(0);
            $table->tinyInteger('period8')->default(0);
            $table->tinyInteger('period9')->default(0);
            $table->tinyInteger('period10')->default(0);
            $table->tinyInteger('period11')->default(0);
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
        Schema::dropIfExists('rentlogs');
    }
}
