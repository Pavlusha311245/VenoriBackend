<?php

use App\Http\Controllers\CategoryController;
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
//   Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class);
    Route::get('/details', 'App\Http\Controllers\UserController@showProfile');
});

Route::post('/login','App\Http\Controllers\Auth\AuthController@postLogin')->middleware('logging.request');
Route::post('/registration','App\Http\Controllers\Auth\AuthController@postRegistration')->middleware('logging.request');
Route::post('/forgot','App\Http\Controllers\Auth\AuthController@postForgotPassword')->middleware('logging.request');
Route::post('/reset','App\Http\Controllers\Auth\AuthController@postResetPassword')->middleware('logging.request');

