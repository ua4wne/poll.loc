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

        Route::match(['get','post'],'/visit',['uses'=>'VisitController@index','as'=>'visit-report']);
        Route::post('/visit-table',['uses'=>'VisitController@table','as'=>'visitTable']);
        Route::post('/analise',['uses'=>'VisitController@analise','as'=>'analise']);

        Route::match(['get','post'],'/work',['uses'=>'WorkController@index','as'=>'work-report']);
        Route::post('/rentsel',['uses'=>'WorkController@select','as'=>'rentsel']);

        Route::get('/main-report',['uses'=>'MainCounterController@index','as'=>'cnt-main-report']);
        Route::post('/main-graph',['uses'=>'MainCounterController@graph','as'=>'main-graph']);
        Route::post('/main-pie',['uses'=>'MainCounterController@pie_graph','as'=>'main-pie']);
        Route::post('/main-table',['uses'=>'MainCounterController@table','as'=>'main-table']);

        Route::get('/own-report',['uses'=>'OwnCounterController@index','as'=>'own-report']);
        Route::post('/own-graph',['uses'=>'OwnCounterController@graph','as'=>'own-graph']);
        Route::post('/own-pie',['uses'=>'OwnCounterController@pie_graph','as'=>'own-pie']);
        Route::post('/own-table',['uses'=>'OwnCounterController@table','as'=>'own-table']);

        Route::get('/rent-report',['uses'=>'RenterCounterController@index','as'=>'rent-report']);
        Route::post('/rent-graph',['uses'=>'RenterCounterController@graph','as'=>'rent-graph']);
        Route::post('/rent-pie',['uses'=>'RenterCounterController@pie_graph','as'=>'rent-pie']);
        Route::post('/rent-table',['uses'=>'RenterCounterController@table','as'=>'rent-table']);

    });

});
