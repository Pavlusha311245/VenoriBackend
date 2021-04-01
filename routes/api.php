<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::group(['middleware' => ['auth:api']], function() {
//   Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class);
    Route::post('/logout', 'App\Http\Controllers\Auth\AuthController@postLogout');
    Route::get('/details', 'App\Http\Controllers\UserController@showProfile');
    Route::resource('places', PlaceController::class);
    Route::get('/booking_history', 'App\Http\Controllers\OrderController@getBookingHistory');
//    Route::get('/users/{id}/booking_history', 'App\Http\Controllers\Auth\AuthController@getBookingHistoryById');
    Route::resource('places', PlaceController::class);
    Route::get('/users/{id}/location', 'App\Http\Controllers\UserController@getUserLocation');
});

Route::post('/login','App\Http\Controllers\Auth\AuthController@postLogin')->middleware('logging.request');
Route::post('/registration','App\Http\Controllers\Auth\AuthController@postRegistration')->middleware('logging.request');
Route::post('/forgot','App\Http\Controllers\Auth\AuthController@postForgotPassword')->middleware('logging.request');
Route::post('/reset','App\Http\Controllers\Auth\AuthController@postResetPassword')->middleware('logging.request');

