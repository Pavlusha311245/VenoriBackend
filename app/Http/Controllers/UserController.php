<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for adding, deleting, updating and showing users
 *
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * The method returns a list of all users
     *
     * @return Response
     */
    public function index()
    {
        return User::paginate(5);
    }

    public function create(Request $request)
    {
        $validData = $request->validate([
            'first_name' => 'required|min:2',
            'second_name' => 'required|min:2',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:8',
        ]);

        $user = User::create($validData);

        if ($user) {
            Auth::login($user);
            return redirect('/admin/dashboard')->with('success', 'Register successful');
        }

        return redirect('/register')->withErrors('formError', 'Register failed');
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function edit(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'min:2',
            'second_name' => 'min:2',
        ]);

        $user = User::findOrFail($id);
        $user->update($request->all());
        $user->save();

        return redirect("/admin/users/$id")->with(['success', 'User was updated']);
    }

    /**
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function remove($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect("/admin/users/")->with(['success', 'User was deleted']);
    }

    /**
     * The method adds a new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|min:2',
            'second_name' => 'required|min:2',
            'email' => 'required|email|unique:users|max:255',
            'address_address' => 'string',
            'address_latitude' => 'double',
            'address_longitude' => 'double',
            'avatar' => 'string',
            'password' => 'required|min:8',
        ]);

        $user = User::create($request->all());

        return response()->json($user, 201);
    }

    /**
     * The method returns information about auth user
     * @return JsonResponse
     */
    public function showProfile()
    {
        return User::findOrFail(auth()->user()->id);
    }

    /**
     * The method updates the data of the user
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'min:2',
            'second_name' => 'min:2',
            'email' => 'max:255|email|unique:users',
            'address_address' => 'string',
            'address_latitude' => 'double',
            'address_longitude' => 'double',
            'avatar' => 'string',
            'password' => 'min:8',
        ]);

        $user->update($request->all());

        return response()->json($user, 200);
    }

    /**
     * The method removes user
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User is deleted successfully'], 200);
    }

    /**
     * The method uses the search service to enter the user's location into the database
     * @param Request $request
     * @return JsonResponse
     */
    public function location(Request $request)
    {
        $userLocation = $request->validate([
            'address_full' => 'required|string',
            'address_lat' => 'required|numeric',
            'address_lot' => 'required|numeric'
        ]);

        $user = User::findOrFail(auth()->id());
        $user->update($userLocation);
        $user->save();

        return response()->json($userLocation, 200);
    }
}
