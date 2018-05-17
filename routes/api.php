<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});


Route::group(['middleware' => ['api']], function () {

	Route::get('/', 'HomeController@index');

	Route::resource('faq', 'FAQController');
	Route::get('faq-list', 'FAQController@getList');

	Route::get('messages', 'MessagesController@index')->name('messages.index');
	Route::get('messages-list', 'MessagesController@getMessages');
	Route::post('send-message/{userVKId}', 'MessagesController@sendMessage');
    Route::post('clear-attempts/{id}', 'MessagesController@clearAttempts');

    Route::post('notify', 'NotifyController@notify');
    Route::get('cities', 'NotifyController@cities');
    Route::get('notifications', 'NotifyController@notifications');
    Route::get('usersCount', 'NotifyController@usersCount');

	Route::get('users/{id}', 'UsersController@userById');
	Route::get('users/{id}/messages', 'UsersController@usersMessages');
    Route::get('users', 'UsersController@users');
    Route::get('activities/{userId}', 'UsersController@getActivitiesByUserId');
    Route::get('users/subscribers/cities', 'UsersController@subscriberCities');

    Route::get('fests', 'FestController@festLIst');
    Route::get('fests/all', 'FestController@all');
    Route::put('fests', 'FestController@create');
    Route::post('fests/{id}', 'FestController@edit');
    Route::get('fests/{id}', 'FestController@fest');
    Route::delete('fests/{id}', 'FestController@remove');

    Route::get('photos', 'PhotoController@photos');
    Route::put('photos', 'PhotoController@create');
    Route::post('photos/{id}', 'PhotoController@edit');
    Route::get('photo/votes', 'PhotoController@voteResults');
    Route::get('photo/{id}', 'PhotoController@photo');


    Route::get('participants/{id}', 'ParticipantController@getById');
    Route::get('participants/all', 'ParticipantController@getAll');
    Route::post('participants/{id}', 'ParticipantController@edit');
    Route::put('participants', 'ParticipantController@create');
});