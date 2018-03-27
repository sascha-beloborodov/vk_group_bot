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


});

//Route::get('admin', function () {
//	if (\Illuminate\Support\Facades\Auth::check()) {
//		return redirect('admin/home');
//	}
//	return redirect('admin/login');
//});

//Auth::routes();
