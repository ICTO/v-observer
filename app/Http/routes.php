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
    // @TODO : create new user for admins only

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
    // questionaire
    Route::get('questionaire/create/{owner_id?}', 'Observation\QuestionaireController@getCreateQuestionaire');
    Route::post('questionaire/create', 'Observation\QuestionaireController@postCreateQuestionaire');
    Route::get('questionaire/{id}', 'Observation\QuestionaireController@getQuestionaire');
    Route::get('questionaire/{id}/edit', 'Observation\QuestionaireController@getEditQuestionaire');
    Route::post('questionaire/{id}/edit', 'Observation\QuestionaireController@postEditQuestionaire');
    Route::get('questionaire/{id}/interval', 'Observation\QuestionaireController@getEditInterval');
    Route::post('questionaire/{id}/interval', 'Observation\QuestionaireController@postEditInterval');
    Route::get('questionaire/{id}/remove', 'Observation\QuestionaireController@getRemoveQuestionaire');
    Route::post('questionaire/{id}/remove', 'Observation\QuestionaireController@postRemoveQuestionaire');
    Route::get('questionaire/{id}/blocks', 'Observation\QuestionaireController@getBlocks');
    Route::get('questionaire/{id}/block/create/{type}/{parent_id?}', 'Observation\QuestionaireController@getCreateBlock');
    Route::post('questionaire/{id}/block/create/{type}/{parent_id?}', 'Observation\QuestionaireController@postCreateBlock');
    Route::get('block/{id}/edit', 'Observation\QuestionaireController@getEditBlock');
    Route::post('block/{id}/edit', 'Observation\QuestionaireController@postEditBlock');
    Route::get('block/{id}/remove', 'Observation\QuestionaireController@getRemoveBlock');
    Route::post('block/{id}/remove', 'Observation\QuestionaireController@postRemoveBlock');
    // video
    Route::get('questionaire/{id}/video/create/{type}', 'Observation\VideoController@getCreateVideo');
    Route::post('questionaire/{id}/video/create/{type}', 'Observation\VideoController@postCreateVideo');
    Route::get('video/{id}', 'Observation\VideoController@getVideo');
    Route::get('video/{id}/edit', 'Observation\VideoController@getEditVideo');
    Route::post('video/{id}/edit', 'Observation\VideoController@postEditVideo');
    Route::get('video/{id}/remove', 'Observation\VideoController@getRemoveVideo');
    Route::post('video/{id}/remove', 'Observation\VideoController@postRemoveVideo');
    Route::get('video/{id}/upload_finished', 'Observation\VideoController@getUploadFinished');
    Route::get('video/{id}/upload_progress', 'Observation\VideoController@getUploadProgress');
    Route::get('video/{id}/transcript', 'Observation\VideoController@getEditTranscript');
    Route::post('video/{id}/transcript', 'Observation\VideoController@postEditTranscript');
    Route::get('video/{id}/analysis', 'Observation\VideoController@getAnalysis');
    Route::post('video/{id}/analysis/{part_id}/{block_id}', 'Observation\VideoController@postAnalysisBlock');

});
