<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');
Route::get('auth/cas', 'Auth\AuthController@getCas');

// Password reset link request routes...
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

// Requires Authentication
Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard/{id?}', 'User\UserController@getDashboard');
    Route::get('/profile/{id?}', 'User\UserController@getProfile');
    Route::get('/profile/{id}/edit', 'User\UserController@getEditProfile');
    Route::post('/profile/{id}/edit', 'User\UserController@postEditProfile');
    Route::get('/group', 'User\UserController@getGroups');
    Route::get('/group/create', 'User\UserController@getCreateGroup');
    Route::post('/group/create', 'User\UserController@postCreateGroup');
    Route::get('/group/add/{id}', 'User\UserController@getAddUser');
    Route::post('/group/add', 'User\UserController@postAddUser');
    Route::get('/user/create/{group_id?}', 'User\UserController@getCreateUser');
    Route::post('/user/create', 'User\UserController@postCreateUser');
});
