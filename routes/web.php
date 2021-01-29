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

Route::get('/', 'BlogController@index'); // главная

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::resource('articles', 'ArticleController');

/* Маршруты Vue добавление картинки */
Route::post('/image/upload/', 'ImageController@upload')->name('image.upload');
Route::post('/image/delete/', 'ImageController@delete')->name('image.delete');

Route::get('/no_access', function () {
  return view('errors.no_access' );
});

Route::resource('comments', 'CommentController');