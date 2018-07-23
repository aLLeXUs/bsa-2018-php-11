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

Route::prefix('v1')->group(function () {
    Route::post('/lots', 'Api\LotsController@store')->name('addLot');
    Route::post('/trades', 'Api\LotsController@buy')->name('buyLot');
    Route::get('/lots/{id}', 'Api\LotsController@show')->name('getLot');
    Route::get('/lots', 'Api\LotsController@index')->name('getLots');
});