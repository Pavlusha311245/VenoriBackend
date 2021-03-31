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
//    Route::resource('roles', RoleController::class);
////   Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class);
    Route::get('/details', 'App\Http\Controllers\UserController@showProfile');
});

Route::post('/login','App\Http\Controllers\Auth\AuthController@postLogin')->middleware('request.logging');
Route::post('/registration','App\Http\Controllers\Auth\AuthController@postRegistration')->middleware('request.logging');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
