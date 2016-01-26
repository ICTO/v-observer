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
    // questionnaire
    Route::get('questionnaire/create/{owner_id?}', 'Observation\QuestionnaireController@getCreateQuestionnaire');
    Route::post('questionnaire/create', 'Observation\QuestionnaireController@postCreateQuestionnaire');
    Route::get('questionnaire/import/{owner_id?}', 'Observation\QuestionnaireController@getImportQuestionnaire');
    Route::post('questionnaire/import', 'Observation\QuestionnaireController@postImportQuestionnaire');
    Route::get('questionnaire/{id}', 'Observation\QuestionnaireController@getQuestionnaire');
    Route::get('questionnaire/{id}/edit', 'Observation\QuestionnaireController@getEditQuestionnaire');
    Route::post('questionnaire/{id}/edit', 'Observation\QuestionnaireController@postEditQuestionnaire');
    Route::post('questionnaire/{id}/order', 'Observation\QuestionnaireController@postOrderBlocks');
    Route::get('questionnaire/{id}/interval', 'Observation\QuestionnaireController@getEditInterval');
    Route::post('questionnaire/{id}/interval', 'Observation\QuestionnaireController@postEditInterval');
    Route::get('questionnaire/{id}/remove', 'Observation\QuestionnaireController@getRemoveQuestionnaire');
    Route::post('questionnaire/{id}/remove', 'Observation\QuestionnaireController@postRemoveQuestionnaire');
    Route::get('questionnaire/{id}/blocks', 'Observation\QuestionnaireController@getBlocks');
    Route::get('questionnaire/{id}/export', 'Observation\QuestionnaireController@getExportQuestionnaire');
    Route::get('questionnaire/{id}/block/create/{type}/{parent_id?}', 'Observation\QuestionnaireController@getCreateBlock');
    Route::post('questionnaire/{id}/block/create/{type}/{parent_id?}', 'Observation\QuestionnaireController@postCreateBlock');
    Route::get('block/{id}/edit', 'Observation\QuestionnaireController@getEditBlock');
    Route::post('block/{id}/edit', 'Observation\QuestionnaireController@postEditBlock');
    Route::get('block/{id}/remove', 'Observation\QuestionnaireController@getRemoveBlock');
    Route::post('block/{id}/remove', 'Observation\QuestionnaireController@postRemoveBlock');
    // video
    Route::get('questionnaire/{id}/video/create/{type}', 'Observation\VideoController@getCreateVideo');
    Route::post('questionnaire/{id}/video/create/{type}', 'Observation\VideoController@postCreateVideo');
    Route::get('questionnaire/{questionnaire_id}/video/{id}', 'Observation\VideoController@getVideo');
    Route::get('questionnaire/{questionnaire_id}/video/{id}/edit', 'Observation\VideoController@getEditVideo');
    Route::post('questionnaire/{questionnaire_id}/video/{id}/edit', 'Observation\VideoController@postEditVideo');
    Route::get('questionnaire/{questionnaire_id}/video/{id}/remove', 'Observation\VideoController@getRemoveVideo');
    Route::post('questionnaire/{questionnaire_id}/video/{id}/remove', 'Observation\VideoController@postRemoveVideo');
    Route::get('questionnaire/{questionnaire_id}/video/{id}/upload_finished', 'Observation\VideoController@getUploadFinished');
    Route::get('questionnaire/{questionnaire_id}/video/{id}/upload_progress', 'Observation\VideoController@getUploadProgress');
    Route::get('questionnaire/{questionnaire_id}/video/{id}/transcript', 'Observation\VideoController@getEditTranscript');
    Route::post('questionnaire/{questionnaire_id}/video/{id}/transcript', 'Observation\VideoController@postEditTranscript');
    Route::get('questionnaire/{questionnaire_id}/video/{id}/analysis', 'Observation\VideoController@getAnalysis');
    Route::post('questionnaire/{questionnaire_id}/video/{id}/analysis', 'Observation\VideoController@postAnalysisBlock');
    Route::get('questionnaire/{questionnaire_id}/video/{id}/analysis/finished', 'Observation\VideoController@getAnalysisFinished');
    Route::get('questionnaire/{questionnaire_id}/video/{id}/analysis/export', 'Observation\VideoController@getAnalysisExportType');
    Route::post('questionnaire/{questionnaire_id}/video/{id}/analysis/export', 'Observation\VideoController@postAnalysisExportType');
    Route::get('questionnaire/{questionnaire_id}/video/{id}/analysis/export/{type}', 'Observation\VideoController@getAnalysisExport');

});
