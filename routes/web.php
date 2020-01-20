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

Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
//activate
Route::get('/activate','Auth\LoginController@activate');

Route::middleware(['auth'])->group(function(){
    Route::get('/', 'MainController@index')->name('main');
});

Auth::routes();
