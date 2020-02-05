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
        Route::match(['get','post'],'/mainlog/add',['uses'=>'MainCounterController@create','as'=>'add_mainVal']);

        Route::get('/own-counter',['uses'=>'OwnLogController@index','as'=>'own-counter']);
        Route::match(['get','post'],'/ownlog/add',['uses'=>'OwnLogController@create','as'=>'add_ownVal']);

        Route::get('/renters-counter',['uses'=>'RentersCounterController@index','as'=>'renters-counter']);
        Route::match(['get','post'],'/rentlog/add',['uses'=>'RentersCounterController@create','as'=>'add_rentVal']);
        Route::post('/selrenters',['uses'=>'RentersCounterController@select','as'=>'sel_renters']);
        Route::post('/tblrenter',['uses'=>'RentersCounterController@table','as'=>'table_renter']);

        Route::match(['get','post'],'/initmain',['uses'=>'InitMainController@index','as'=>'initmain']);
        Route::match(['get','post'],'/initown',['uses'=>'InitOwnController@index','as'=>'initown']);
        Route::match(['get','post'],'/initcounter',['uses'=>'InitCounterController@index','as'=>'initcounter']);

        Route::get('/billing',['uses'=>'BillingController@index','as'=>'billing']);
        Route::match(['get','post'],'/rent-calculate',['uses'=>'BillingController@calculate','as'=>'rent-calculate']);
        Route::match(['get','post'],'/rent-period',['uses'=>'BillingController@period','as'=>'rent-period']);

    });

});
