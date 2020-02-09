<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logforms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('data');
            $table->integer('form_id');
            $table->integer('question_id');
            $table->integer('answer_id');
            $table->string('answer',100);
            $table->integer('user_id');
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
        Schema::dropIfExists('logforms');
    }
}
