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

    //cities/ группа обработки роутов cities
    Route::group(['prefix'=>'cities'], function(){
        Route::get('/',['uses'=>'CityController@index','as'=>'cities']);
        //cities/add
        Route::match(['get','post'],'/add',['uses'=>'CityController@create','as'=>'cityAdd']);
        //cities/ajax/edit_counter
        Route::post('/ajax/edit',['uses'=>'Ajax\CityController@edit','as'=>'editCity']);
        //cities/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\CityController@delete','as'=>'deleteCity']);
    });

    //materials/ группа обработки роутов materials
    Route::group(['prefix'=>'materials'], function(){
        Route::get('/',['uses'=>'MaterialController@index','as'=>'materials']);
        //materials/add
        Route::match(['get','post'],'/add',['uses'=>'MaterialController@create','as'=>'materialAdd']);
        //materials/ajax/edit
        Route::post('/ajax/edit',['uses'=>'Ajax\MaterialController@edit','as'=>'editMaterial']);
        //materials/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\MaterialController@delete','as'=>'deleteMaterial']);
    });

    //tvsources/ группа обработки роутов tvsources
    Route::group(['prefix'=>'tvsources'], function(){
        Route::get('/',['uses'=>'TvsourceController@index','as'=>'tvsources']);
        //tvsources/add
        Route::match(['get','post'],'/add',['uses'=>'TvsourceController@create','as'=>'tvsourceAdd']);
        //tvsources/ajax/edit
        Route::post('/ajax/edit',['uses'=>'Ajax\TvsourceController@edit','as'=>'editTvsource']);
        //tvsources/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\TvsourceController@delete','as'=>'deleteTvsource']);
    });
});