<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ScheduleController;
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
    Route::apiResource('users', UserController::class, ['except' => ['create', 'edit', 'remove']]);
    Route::apiResource('categories', CategoryController::class, ['except' => ['create', 'edit', 'remove']]);
    Route::apiResource('products', ProductController::class, ['except' => ['create', 'edit', 'remove']]);
    Route::apiResource('places', PlaceController::class, ['except' => ['create', 'edit', 'remove']]);
    Route::apiResource('reviews', ReviewController::class, ['except' => ['create', 'edit', 'remove']]);
    Route::apiResource('favourites', FavouriteController::class,['except' => ['create', 'edit', 'remove']]);
    Route::apiResource('schedules', ScheduleController::class, ['except' => ['create', 'edit', 'remove']]);
    Route::apiResource('comments', CommentController::class, ['except' => ['create', 'edit', 'remove']]);

    Route::get('/user/details', 'App\Http\Controllers\UserController@showProfile');
    Route::get('/get_info', 'App\Http\Controllers\AppInfoController@getInfo');
    Route::get('/booking_history', 'App\Http\Controllers\OrderController@getBookingHistory');
    Route::get('/orders', 'App\Http\Controllers\OrderController@getActiveOrders');
    Route::get('/products/{name}', 'App\Http\Controllers\ProductController@getProduct');
    Route::get('/places/{id}/reviewsCount', 'App\Http\Controllers\PlaceController@reviewsCount');
    Route::get('/places/{id}/menu', 'App\Http\Controllers\PlaceController@menu');
    Route::get('/user/favourites', 'App\Http\Controllers\FavouriteController@showUserFavourites');
    Route::get('/places/{id}/schedule', 'App\Http\Controllers\ScheduleController@scheduleByPlaceId');
    Route::get('/places/{id}/reviews', 'App\Http\Controllers\ReviewController@reviewsByPlaceId');
    Route::get('/user/reviews', 'App\Http\Controllers\ReviewController@reviewsByUserId');
    Route::get('/reviews/{id}/comments', 'App\Http\Controllers\CommentController@commentsByReviewId');

    Route::post('/places/{place_id}/reservation', 'App\Http\Controllers\ReservationController@availableTime');
    Route::post('/places/{place_id}/reserve', 'App\Http\Controllers\ReservationController@tableReserve');
    Route::post('/places/{id}/uploadImage', 'App\Http\Controllers\PlaceController@uploadImage');
    Route::post('/orders/{order_id}', 'App\Http\Controllers\OrderController@cancelOrder');
    Route::post('/logout', 'App\Http\Controllers\Auth\AuthController@logout');
    Route::post('/users/{id}/uploadAvatar', 'App\Http\Controllers\UserController@uploadAvatar');
    Route::post('/users/{id}/favourites', 'App\Http\Controllers\FavouriteController@showUserFavourites');
    Route::post('/categories/{id}/uploadImage', 'App\Http\Controllers\CategoryController@uploadImage');
    Route::post('/products/import', 'App\Http\Controllers\ProductController@import');

    Route::put('/user/location', 'App\Http\Controllers\UserController@location');
});

Route::group(['middleware' => 'logging'], function () {
    Route::post('/login', 'App\Http\Controllers\Auth\AuthController@login');
    Route::post('/register', 'App\Http\Controllers\Auth\AuthController@register');
    Route::post('/forgot', 'App\Http\Controllers\Auth\AuthController@forgotPassword');
    Route::post('/reset', 'App\Http\Controllers\Auth\AuthController@resetPassword');
});
