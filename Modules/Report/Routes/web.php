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

    //reports/ группа обработки роутов reports
    Route::group(['prefix'=>'reports'], function(){

        Route::get('/it-cost',['uses'=>'ItCostController@index','as'=>'it-cost']);
        Route::post('/it-cost/graph',['uses'=>'ItCostController@graph','as'=>'it_costGraph']);
        Route::post('/it-cost/table',['uses'=>'ItCostController@table','as'=>'it_costTable']);

        Route::get('/connections',['uses'=>'InetConnectionController@index','as'=>'inet-conn']);

    });

});
