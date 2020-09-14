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
Route::post('/control',['uses'=>'RequestController@control','as'=>'control']);

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

    //events/ группа обработки роутов events
    Route::group(['prefix'=>'events'], function(){
        Route::get('/',['uses'=>'EventLogController@index','as'=>'events']);
        //events/delete
        Route::post('/delete',['uses'=>'EventLogController@delete','as'=>'eventDelete']);
        Route::get('/view-requests',['uses'=>'RequestController@index','as'=>'view-requests']);
        Route::post('/delrequest',['uses'=>'RequestController@delete','as'=>'requestDel']);
    });

    //users/ группа обработки роутов users
    Route::group(['prefix'=>'users'], function(){
        Route::get('/',['uses'=>'UserController@index','as'=>'users']);
        //users/add
        Route::match(['get','post'],'/add',['uses'=>'UserController@create','as'=>'userAdd']);
        //users/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'UserController@edit','as'=>'userEdit']);
        //users/reset
        Route::get('/reset/{id}',['uses'=>'UserController@resetPass','as'=>'userReset']);
        //users/ajax/edit
        Route::post('/ajax/edit',['uses'=>'Ajax\UserController@switchLogin','as'=>'switchLogin']);
        //users/ajax/edit_login
        Route::post('/ajax/edit_login',['uses'=>'Ajax\UserController@editLogin','as'=>'editLogin']);
        //users/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\UserController@delete','as'=>'deleteLogin']);
        //users/ajax/add_role
        Route::post('/ajax/add_role',['uses'=>'Ajax\UserController@addRole','as'=>'addRole']);
        //users/ajax/get_role
        Route::post('/ajax/get_role',['uses'=>'Ajax\UserController@getRole','as'=>'getRole']);
    });

    //ecounters/ группа обработки роутов ecounters
    Route::group(['prefix'=>'ecounters'], function(){
        Route::get('/',['uses'=>'EcounterController@index','as'=>'ecounters']);
        //ecounters/add
        Route::match(['get','post'],'/add',['uses'=>'EcounterController@create','as'=>'ecounterAdd']);
        //ecounters/ajax/edit_counter
        Route::post('/ajax/edit',['uses'=>'Ajax\EcounterController@edit','as'=>'editEcounter']);
        //ecounters/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\EcounterController@delete','as'=>'deleteEcounter']);
    });

    //own-ecounters/ группа обработки роутов own-ecounters
    Route::group(['prefix'=>'own-ecounters'], function(){
        Route::get('/',['uses'=>'OwnEcounterController@index','as'=>'own-ecounters']);
        //own-ecounters/add
        Route::match(['get','post'],'/add',['uses'=>'OwnEcounterController@create','as'=>'own-ecounterAdd']);
        //own-ecounters/ajax/edit_counter
        Route::post('/ajax/edit',['uses'=>'Ajax\OwnEcounterController@edit','as'=>'editOwnEcounter']);
        //own-ecounters/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\OwnEcounterController@delete','as'=>'deleteOwnEcounter']);
    });

    //describers/ группа обработки роутов describers
    Route::group(['prefix'=>'describers'], function(){
        Route::get('/',['uses'=>'DescriberController@index','as'=>'describers']);
        //describers/add
        Route::match(['get','post'],'/add',['uses'=>'DescriberController@create','as'=>'describerAdd']);
        //describers/ajax/edit
        Route::post('/ajax/edit',['uses'=>'Ajax\DescriberController@switchStatus','as'=>'switchStatus']);
        //describers/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\DescriberController@delete','as'=>'deleteDescriber']);
    });

    //places/ группа обработки роутов places
    Route::group(['prefix'=>'places'], function(){
        Route::get('/',['uses'=>'PlaceController@index','as'=>'places']);
        //places/add
        Route::match(['get','post'],'/add',['uses'=>'PlaceController@create','as'=>'placeAdd']);
        //places/ajax/edit
        Route::post('/ajax/edit',['uses'=>'Ajax\PlaceController@edit','as'=>'editPlace']);
        //places/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\PlaceController@delete','as'=>'deletePlace']);
    });

    //backups/ группа обработки роутов backups
    Route::group(['prefix'=>'backups'], function(){
        Route::get('/',['uses'=>'BackupController@index','as'=>'backups']);
    });
});
