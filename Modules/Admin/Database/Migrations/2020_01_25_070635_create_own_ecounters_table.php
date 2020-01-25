<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOwnEcountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('own_ecounters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',50);
            $table->string('text',100)->nullable();
            $table->float('koeff');
            $table->float('tarif')->default(3.7);
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
        Schema::dropIfExists('own_ecounters');
    }
}
