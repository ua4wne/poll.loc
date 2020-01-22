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
    Route::prefix('roles')->group(function () {
            Route::get('/',['uses'=>'RoleController@index','as'=>'roles']);
            //roles/add
            Route::match(['get','post'],'/add',['uses'=>'RoleController@create','as'=>'roleAdd']);
            //roles/edit
            Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'RoleController@edit','as'=>'roleEdit']);
            //roles/ajax/get_action
            Route::post('/ajax/get_action',['uses'=>'Ajax\ActionController@getAction','as'=>'getAction']);
            //roles/ajax/add_action
            Route::post('/ajax/add_action',['uses'=>'Ajax\ActionController@addAction','as'=>'addAction']);
        });

    //actions/ группа обработки роутов actions
    Route::group(['prefix'=>'actions'], function(){
        Route::get('/',['uses'=>'ActionController@index','as'=>'actions']);
        //actions/add
        Route::match(['get','post'],'/add',['uses'=>'ActionController@create','as'=>'actionAdd']);
        //actions/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'ActionController@edit','as'=>'actionEdit']);
    });
});
