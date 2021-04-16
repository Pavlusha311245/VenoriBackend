<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

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
//    Log::channel('abuse')->info('API endpoint abuse', [
//        'user_id' => 1
//    ]);
    return view('home');
});

Route::group(['middleware' => ['auth'], 'prefix' => 'admin'], function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/products', function () {
        return view('products');
    });

    Route::get('/places', function () {
        return view('places');
    });

    Route::get('/users', function () {
        return view('users.index', ['users' => User::all()]);
    });
    Route::get('/users/create', function () {
        return view('users.create', ['roles' => Role::all()]);
    });
    Route::get('/users/{id}', function ($id) {
        return view('users.show', ['user' => User::findOrFail($id)]);
    });
    Route::get('/users/{id}/edit', function ($id) {
        return view('users.edit', ['user' => User::findOrFail($id)]);
    });
    Route::get('/users/{id}/delete', function ($id) {
        return view('users.delete', ['user' => User::findOrFail($id)]);
    });

    Route::post('/users/create', 'App\Http\Controllers\UserController@create');
    Route::post('/users/{id}/edit', 'App\Http\Controllers\UserController@edit');
    Route::post('/users/{id}/delete', 'App\Http\Controllers\UserController@remove');

    Route::post('/places/create', 'App\Http\Controllers\PlaceController@create');
    Route::post('/places/{id}/edit', 'App\Http\Controllers\PlaceController@edit');
    Route::post('/places/{id}/delete', 'App\Http\Controllers\PlaceController@remove');

    Route::post('/products/create', 'App\Http\Controllers\ProductController@create');
    Route::post('/products/{id}/edit', 'App\Http\Controllers\ProductController@edit');
    Route::post('/products/{id}/delete', 'App\Http\Controllers\ProductController@remove');
});

Route::get('/login', function () {
    if (Auth::check())
        return view('home');
    return view('auth.login');
})->name('login');

Route::post('/login', 'App\Http\Controllers\Auth\AuthController@loginAdmin');

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/login');
});

//Route::get('/register', function () {
//    return view('auth.registration');
//});

