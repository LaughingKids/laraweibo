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

Route::get('/', 'Views\PagesController@home')->name('home');

Route::get('/help', 'Views\PagesController@help')->name('help');

Route::get('/about', 'Views\PagesController@about')->name('about');

Route::get('signup','Views\UsersController@create')->name('signup');

Route::resource('users', 'Views\UsersController');

Route::get('login', 'SessionsController@create')->name('login');
Route::post('login', 'SessionsController@store')->name('login');
Route::delete('logout', 'SessionsController@destroy')->name('logout');
