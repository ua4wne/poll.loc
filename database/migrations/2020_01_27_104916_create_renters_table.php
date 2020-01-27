<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('renters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title',100);
            $table->string('name',100);
            $table->string('area',20);
            $table->string('agent',50);
            $table->string('phone1',20)->nullable();
            $table->string('phone2',20)->nullable();
            $table->string('encounter',20)->unique();
            $table->float('koeff')->default(5.8);
            $table->integer('place_id')->unsigned();
            $table->foreign('place_id')->references('id')->on('places');
            $table->smallInteger('status')->default(1);
            $table->integer('division_id')->unsigned();
            $table->foreign('division_id')->references('id')->on('divisions');
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
        Schema::dropIfExists('renters');
    }
}
