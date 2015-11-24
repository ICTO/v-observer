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

Route::get('/', 'Pages\PageController@getWelcome');
Route::get('setup', 'Pages\PageController@getSetup');
Route::post('setup', 'Pages\PageController@postSetup');

// Authentication routes...
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/cas', 'Auth\AuthController@getCas');

// Password reset link request routes...
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes...
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

// Requires Authentication
Route::group(['middleware' => 'auth'], function () {

    /** User **/

    // Auth routes
    Route::get('auth/logout', 'Auth\AuthController@getLogout');

    // User routes
    Route::get('profile/{id?}', 'User\UserController@getProfile');
    Route::get('profile/{id}/edit', 'User\UserController@getEditProfile');
    Route::post('profile/{id}/edit', 'User\UserController@postEditProfile');
    Route::get('profile/{id}/remove', 'User\UserController@getRemoveProfile');
    Route::post('profile/{id}/remove', 'User\UserController@postRemoveProfile');
    Route::get('dashboard/{id?}', 'User\UserController@getDashboard');

    // Group routes
    Route::get('group', 'User\UserController@getGroups');
    Route::get('group/create', 'User\UserController@getCreateGroup');
    Route::post('group/create', 'User\UserController@postCreateGroup');

    // Group users routes
    Route::get('group/{group_id}/user/add', 'User\UserController@getAddUser');
    Route::post('group/{group_id}/user/add', 'User\UserController@postAddUser');
    Route::get('group/{group_id}/user/create', 'User\UserController@getCreateUser');
    Route::post('group/{group_id}/user/create', 'User\UserController@postCreateUser');
    Route::get('group/{group_id}/user/{user_id}/remove', 'User\UserController@getRemoveUser');
    Route::post('group/{group_id}/user/{user_id}/remove', 'User\UserController@postRemoveUser');
    Route::get('group/{group_id}/user/{user_id}/role/{role}', 'User\UserController@getRoleUser');
    Route::post('group/{group_id}/user/{user_id}/role/{role}', 'User\UserController@postRoleUser');

    /** Obeservation tool **/
});
