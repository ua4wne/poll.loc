<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitorlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitorlogs', function (Blueprint $table) {
            $table->increments('id');
            $table->date('data');
            $table->string('hours', 2);
            $table->integer('fw');
            $table->integer('bw');
            $table->integer('counter_id');
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
        Schema::dropIfExists('visitorlogs');
    }
}
