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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/market/lots/add', 'LotsController@create')->middleware('auth')->name('createLot');
Route::post('/market/lots/add', 'LotsController@store')->middleware('auth')->name('storeLot');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
