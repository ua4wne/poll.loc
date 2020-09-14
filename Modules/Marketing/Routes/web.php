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

    //forms/ группа обработки роутов forms
    Route::group(['prefix'=>'forms'], function(){
        Route::get('/',['uses'=>'FormController@index','as'=>'forms']);
        //forms/view
        Route::get('/view/{id}',['uses'=>'FormController@view','as'=>'form_view']);
        //save poll
        Route::post('/save-poll',['uses'=>'FormController@storePoll','as'=>'save_poll']);
        //forms/add
        Route::match(['get','post'],'/add',['uses'=>'FormController@create','as'=>'formAdd']);
        //forms/ajax/edit
        Route::post('/ajax/edit',['uses'=>'Ajax\FormController@edit','as'=>'editForm']);
        //forms/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\FormController@delete','as'=>'deleteForm']);
        //forms/media
        Route::match(['get','post'],'/media',['uses'=>'FormController@media','as'=>'media_form']);

        Route::get('/single',['uses'=>'FormController@singleForm','as'=>'singleForm']);
        Route::match(['get','post'],'/group',['uses'=>'FormController@groupForm','as'=>'groupForm']);
        //forms/group-view
        Route::get('/group-view/{id}',['uses'=>'FormController@groupView','as'=>'group_view']);
        //forms/set-form
        Route::get('/set-form',['uses'=>'FormController@setForm','as'=>'set_form']);
    });

    //questions/ группа обработки роутов questions
    Route::group(['prefix'=>'questions'], function(){
        Route::get('/{id}',['uses'=>'QuestionController@index','as'=>'questions']);
        //questions/add
        Route::match(['get','post'],'/add/{id}',['uses'=>'QuestionController@create','as'=>'questionAdd']);
        //questions/ajax/show
        Route::post('/ajax/show',['uses'=>'Ajax\QuestionController@show','as'=>'switchQuestion']);
        //questions/ajax/edit
        Route::post('/ajax/edit',['uses'=>'Ajax\QuestionController@edit','as'=>'editQuestion']);
        //questions/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\QuestionController@delete','as'=>'deleteQuestion']);
    });

    //answers/ группа обработки роутов answers
    Route::group(['prefix'=>'answers'], function(){
        Route::get('/{id}',['uses'=>'AnswerController@index','as'=>'answers']);
        //answers/add
        Route::match(['get','post'],'/add/{id}',['uses'=>'AnswerController@create','as'=>'answerAdd']);
        //answers/edit
        Route::post('/edit',['uses'=>'AnswerController@edit','as'=>'switchAnswer']);
        //answers/delete
        Route::post('/delete',['uses'=>'AnswerController@delete','as'=>'deleteAnswer']);
    });

    //megacounts/ группа обработки роутов megacounts
    Route::group(['prefix'=>'megacount'], function(){
        Route::get('/',['uses'=>'MegacountController@index','as'=>'megacounts']);
        //megacount/add
        Route::match(['get','post'],'/add',['uses'=>'MegacountController@create','as'=>'megacountAdd']);
        //megacount/edit
        Route::post('/edit',['uses'=>'MegacountController@edit','as'=>'editMegacount']);
    });
});
