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
});
