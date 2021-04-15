<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    /**
     * The method adds a new user
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|min:2',
            'second_name' => 'required|min:2',
            'email' => 'required|email|unique:users|max:255',
            'address_full' => 'string',
            'address_lat' => 'double',
            'address_lon' => 'double',
            'avatar' => 'string',
            'password' => 'required|min:8',
        ]);

        $user = User::create($request->all());

        return response()->json($user, 201);
    }

    /**
     * The method returns information about user
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
            'address_full' => 'string',
            'address_lat' => 'double',
            'address_lon' => 'double',
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
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function location(Request $request)
    {
        $userLocation = $request->validate([
            'address_full' => 'required|string',
            'address_lat' => 'required|numeric',
            'address_lon' => 'required|numeric',
        ]);

        $user = User::findOrFail(auth()->id());
        $user->update($userLocation);
        $user->save();

        return response()->json($userLocation, 200);
    }
}
