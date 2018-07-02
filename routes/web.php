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

// $logs = DB::connection('mongodb')->collection('logs')->insert(['request' => 'vk-bot']);

Route::get('/', function () {

	return view('welcome');
});

Route::get('admin/login', 'Auth\\AdminLoginController@showLoginForm');

Route::post('admin/login', 'Auth\\AdminLoginController@login');

Route::group(['prefix' => 'admin', 'middleware' => ['auth.admin']], function () {

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

    Route::get('sunmar/tasks', 'SunmarController@getAllTasks');
    Route::delete('sunmar', 'SunmarController@deleteData');
    Route::get('sunmar/task/{num}', 'SunmarController@getByNum');
    Route::get('sunmar/users', 'SunmarController@getUsers');
    Route::post('sunmar/task/run', 'SunmarController@runTask');
    Route::post('sunmar/task/check', 'SunmarController@checkTask');
    Route::post('sunmar/message', 'SunmarController@message');
    Route::get('sunmar/users/import', 'SunmarController@importUsers');
    
});

use ATehnix\VkClient\Auth;
use ATehnix\VkClient\Client;
use Illuminate\Http\Request;

Route::get('vk_auth', function(Request $request) {
	$api = new Client;
	$auth = new Auth('6485912', 'nbRl9L4vQsLwmR9yS7tN', 'http://dev-kfc-bot-admin.grapheme.ru/vk_auth');
	echo "<a href='{$auth->getUrl()}'>ClickMe<a><br>";
	$token = '-';
	if ($request->get('code')) {
		$token = $auth->getToken($request->get('code'));
	}
//	$api->setDefaultToken($token);
	echo 'Токен<br>' . $token;
});

// Route::get('admin', function () {
// 	if (\Illuminate\Support\Facades\Auth::check()) {
// 		return redirect('admin');
// 	}
// 	return redirect('admin/login');
// });

//Auth::routes();
