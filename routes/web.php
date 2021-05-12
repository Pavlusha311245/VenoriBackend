<?php

use App\Models\Category;
use App\Models\Place;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
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
    return view('home');
});

Route::middleware('auth:web')->prefix('admin')->group(function () {
    Route::middleware('role:Admin')->group(function () {
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
    });

    Route::get('/places/{place_id}/products/create', function ($place_id) {
        return view('products.create', ['products' => Product::all(),
            'categories' => Category::select(['id', 'name'])->get(),
            'place_id' => $place_id]);
    });
    Route::get('/products/{id}', function ($id) {
        return view('products.show', ['products' => Product::findOrFail($id)]);
    });
    Route::get('/products/{id}/edit', function ($id) {
        return view('products.edit', ['products' => Product::findOrFail($id)], [ 'categories' => Category::all()]);
    });
    Route::get('/places/{place_id}/products/{product_id}/delete', function ($place_id, $product_id) {
        return view('products.delete', ['product' => Product::findOrFail($product_id), 'place_id' => Place::findOrFail($place_id)->id]);
    });

    Route::get('/user/resetPassword', function () {
        return view('auth.reset');
    });

    Route::get('/places', function () {
        $places = Place::all();

        if (auth()->user()->hasRole('Manager'))
            $places = auth()->user()->managedPlaces;

        foreach ($places as $place) {
            $menu = [];

            foreach ($place->products as $product)
                $menu[$product->category->name][] = $product;

            $place['menu'] = $menu;
        }

        return view('places.index', ['places' => $places]);
    });
    Route::get('/places/create', function () {
        return view('places.create', ['places' => Place::all()]);
    });
    Route::get('/places/{id}', function ($id) {
        return view('places.show', ['place' => Place::findOrFail($id)]);
    });
    Route::get('/places/{id}/edit', function ($id) {
        return view('places.edit', ['place' => Place::findOrFail($id)]);
    });
    Route::get('/places/{id}/delete', function ($id) {
        return view('places.delete', ['place' => Place::findOrFail($id)]);
    });

    Route::get('/orders', function () {
        return view('orders.index', ['places' => auth()->user()->managedPlaces()]);
    });

    Route::post('/places/create', 'App\Http\Controllers\PlaceController@create');
    Route::post('/places/{id}/edit', 'App\Http\Controllers\PlaceController@edit');
    Route::post('/places/{id}/delete', 'App\Http\Controllers\PlaceController@remove');
    Route::post('/places/{place_id}/products/create', 'App\Http\Controllers\ProductController@create');
    Route::post('/products/{product_id}/edit', 'App\Http\Controllers\ProductController@edit');
    Route::post('/places/{place_id}/products/{product_id}/delete', 'App\Http\Controllers\ProductController@remove');

    Route::post('/user/resetPassword', 'App\Http\Controllers\Auth\AuthController@resetPasswordView');
    Route::post('/user/managed/{id}', 'App\Http\Controllers\PlaceController@addPlaceToManagement');

    Route::delete('/user/managed/{id}', 'App\Http\Controllers\PlaceController@removePlaceFromManagement');
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

