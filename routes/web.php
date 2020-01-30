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

Auth::routes();

Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
//activate
Route::get('/activate','Auth\LoginController@activate');

Route::middleware(['auth'])->group(function(){
    Route::get('/', 'MainController@index')->name('main');

    //divisions/ группа обработки роутов divisions
    Route::group(['prefix'=>'divisions'], function(){
        Route::get('/',['uses'=>'DivisionController@index','as'=>'divisions']);
        //divisions/add
        Route::match(['get','post'],'/add',['uses'=>'DivisionController@create','as'=>'divisionAdd']);
        //divisions/ajax/edit_counter
        Route::post('/ajax/edit',['uses'=>'Ajax\DivisionController@edit','as'=>'editDivision']);
        //divisions/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\DivisionController@delete','as'=>'deleteDivision']);
    });

    //renters/ группа обработки роутов renters
    Route::group(['prefix'=>'renters'], function(){
        Route::get('/',['uses'=>'RenterController@index','as'=>'renters']);
        //renters/view/
        Route::get('/view/{status}',['uses'=>'RenterController@view','as'=>'view_renters']);
        //renters/add
        Route::match(['get','post'],'/add',['uses'=>'RenterController@create','as'=>'renterAdd']);
        //renters/ajax/edit
        Route::post('/ajax/edit',['uses'=>'Ajax\RenterController@edit','as'=>'editRenter']);
        //renters/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\RenterController@delete','as'=>'deleteRenter']);
        //renters/ajax/status
        Route::post('/ajax/status',['uses'=>'Ajax\RenterController@switchRenter','as'=>'switchRenter']);
    });

    //visits/ группа обработки роутов visits
    Route::group(['prefix'=>'visits'], function(){
        Route::get('/',['uses'=>'VisitController@index','as'=>'visits']);
        //visits/add
        Route::match(['get','post'],'/add',['uses'=>'VisitController@create','as'=>'visitAdd']);
        //visits/ajax/edit_counter
        //Route::post('/ajax/edit',['uses'=>'Ajax\VisitController@edit','as'=>'editVisit']);
        //visits/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\VisitController@delete','as'=>'deleteVisit']);
    });
});
