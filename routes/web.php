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

	Route::get('home', 'HomeController@index');

	Route::resource('faq', 'FAQController');
	Route::get('faq-list', 'FAQController@getList');
});

Route::get('admin', function () {
	if (\Illuminate\Support\Facades\Auth::check()) {
		return redirect('admin/home');
	}
	return redirect('admin/login');
});
//Auth::routes();
