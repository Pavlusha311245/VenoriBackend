<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => ['auth:api']], function () {
    Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('places', PlaceController::class);

    Route::get('/details', 'App\Http\Controllers\UserController@showProfile');
    Route::get('/get_info', 'App\Http\Controllers\AppInfoController@getInfo');
    Route::get('/booking_history', 'App\Http\Controllers\OrderController@getBookingHistory');
    Route::get('/users/{id}/location', 'App\Http\Controllers\UserController@getUserLocation');
    Route::get('/places', 'App\Http\Controllers\PlaceController@searchPlace');

    Route::post('/logout', 'App\Http\Controllers\Auth\AuthController@logout');

});

Route::group(['middleware' => 'logging.request'], function () {
    Route::post('/login', 'App\Http\Controllers\Auth\AuthController@login');
    Route::post('/registration', 'App\Http\Controllers\Auth\AuthController@registration');
    Route::post('/forgot', 'App\Http\Controllers\Auth\AuthController@forgotPassword');
    Route::post('/reset', 'App\Http\Controllers\Auth\AuthController@resetPassword');
});
