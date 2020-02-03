<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function() {

    //energy/ группа обработки роутов reports
    Route::group(['prefix'=>'energy'], function(){
        Route::get('/main-counter',['uses'=>'MainCounterController@index','as'=>'main-counter']);
        Route::post('/mainlog/add',['uses'=>'MainCounterController@create','as'=>'add_mainVal']);

    });

});
