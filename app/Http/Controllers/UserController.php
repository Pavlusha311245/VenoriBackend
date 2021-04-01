<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
<<<<<<< HEAD
use Illuminate\Support\Facades\Request;
=======
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
>>>>>>> 4b888b0443c86156c79f3b494144c34f7c824b44

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $users = User::paginate(5);
<<<<<<< HEAD
        return response()->json($users,200);
=======
        return response($users,200);
>>>>>>> 4b888b0443c86156c79f3b494144c34f7c824b44
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'first_name' => 'required|min:2',
            'second_name' => 'required|min:2',
            'email' => 'required|email|unique:users|max:255',
<<<<<<< HEAD
            'address_address' => 'string',
            'address_latitude' => 'double',
            'address_longitude' => 'double',
            'avatar' => 'string',
            'password' => 'required|min:8',
=======
            'password' => 'required|min:8'
>>>>>>> 4b888b0443c86156c79f3b494144c34f7c824b44
        ]);

        $user = User::create($request->all());
        return response($user, 201);
    }

    /**
     * @return JsonResponse
     */
    public function showProfile()
    {
        try {
            return User::findOrFail(Auth::id());
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'User Is Not Found'], 201);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function update(Request $request, User $user): Response
    {
        $request->validate([
            'first_name' => 'min:2',
            'second_name' => 'min:2',
            'email' => 'max:255|unique:users',
<<<<<<< HEAD
            'address_address' => 'string',
            'address_latitude' => 'double',
            'address_longitude' => 'double',
            'avatar' => 'string',
            'password' => 'min:8',
        ]);

        $user->update($request->all());
        return response()->json(['message' => 'User is updated successfully'], 201);
=======
            'password' => 'min:8'
        ]);

        $user->update($request->all());
        return response(['message' => 'User is updated successfully'], 201);
>>>>>>> 4b888b0443c86156c79f3b494144c34f7c824b44
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
<<<<<<< HEAD
            return response()->json(['message' => 'User is deleted successfully'], 200);
        } catch (ModelNotFoundException $ex) {
            return response()->json(['error' => 'User Is Not Found'], 404);
        }
    }

    public function getUserLocation(Request $request, $id)
    {
        $userIp = \request()->ip();
        $geoInfoJSON = json_decode(file_get_contents("http://ip-api.com/json/$userIp?lang=http://ip-api.com/json/$userIp?fields=countryCode"), true);
        if ($geoInfoJSON['status'] != 'fail') {
            try {
                $user = User::findOrFail($id)->first();
            } catch (ModelNotFoundException $ex) {
                return response(['error' => 'User not found'], 404);
            }
            $user->update([
                'address_latitude' => $geoInfoJSON['lat'],
                'address_longitude' => $geoInfoJSON['lon'],
                'address_address' => $geoInfoJSON['country'] . '\\' . $geoInfoJSON['regionName'] . '\\' . $geoInfoJSON['city']
            ]);
            $user->save();
            return response($geoInfoJSON, 200);
        }
        else {
            return  response(['error' => 'Invalid request']);
=======
            return response(['message' => 'User is deleted successfully'], 200);
        } catch (ModelNotFoundException $ex) {
            return response(['error' => 'User not found'], 404);
>>>>>>> 4b888b0443c86156c79f3b494144c34f7c824b44
        }
    }
}
