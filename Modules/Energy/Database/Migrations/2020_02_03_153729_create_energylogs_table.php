<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEnergylogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('energylogs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('renter_id')->unsigned();
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
        Schema::dropIfExists('energylogs');
    }
}
