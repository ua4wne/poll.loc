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

    //unit-groups/ группа обработки роутов unit-groups
    Route::group(['prefix'=>'unit-groups'], function(){
        Route::get('/',['uses'=>'UnitGroupController@index','as'=>'unit-groups']);
        //unit-groups/add
        Route::match(['get','post'],'/add',['uses'=>'UnitGroupController@create','as'=>'unit-groupAdd']);
        //unit-groups/ajax/edit_counter
        Route::post('/ajax/edit',['uses'=>'Ajax\UnitGroupController@edit','as'=>'editUnit-group']);
        //unit-groups/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\UnitGroupController@delete','as'=>'deleteUnit-group']);
    });

    //expenses/ группа обработки роутов expenses
    Route::group(['prefix'=>'expenses'], function(){
        Route::get('/',['uses'=>'ExpenseController@index','as'=>'expenses']);
        //expenses/add
        Route::match(['get','post'],'/add',['uses'=>'ExpenseController@create','as'=>'expenseAdd']);
        //expenses/ajax/edit_counter
        Route::post('/ajax/edit',['uses'=>'Ajax\ExpenseController@edit','as'=>'editExpense']);
        //expenses/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\ExpenseController@delete','as'=>'deleteExpense']);
    });

    //suppliers/ группа обработки роутов suppliers
    Route::group(['prefix'=>'suppliers'], function(){
        Route::get('/',['uses'=>'SupplierController@index','as'=>'suppliers']);
        //suppliers/add
        Route::match(['get','post'],'/add',['uses'=>'SupplierController@create','as'=>'supplierAdd']);
        //suppliers/ajax/edit
        Route::post('/ajax/edit',['uses'=>'Ajax\SupplierController@edit','as'=>'editSupplier']);
        //suppliers/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\SupplierController@delete','as'=>'deleteSupplier']);
    });

    //costs/ группа обработки роутов costs
    Route::group(['prefix'=>'costs'], function(){
        Route::get('/',['uses'=>'CostController@index','as'=>'costs']);
        //costs/add
        Route::match(['get','post'],'/add',['uses'=>'CostController@create','as'=>'costAdd']);
        //costs/ajax/edit
        Route::post('/ajax/edit',['uses'=>'Ajax\CostController@edit','as'=>'editCost']);
        //costs/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\CostController@delete','as'=>'deleteCost']);
    });
});
