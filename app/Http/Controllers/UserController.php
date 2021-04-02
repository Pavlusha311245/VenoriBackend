<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return User::paginate(5);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
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

        return response($user, 201);
    }

    /**
     * @return JsonResponse
     */
    public function showProfile()
    {
        return User::findOrFail(auth()->user()->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return Response
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
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User is deleted successfully'], 200);
    }

    public function getUserLocation(Request $request, $id)
    {
        $userIp = request()->ip();
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
        } else {
            return response(['error' => 'Invalid request']);
        }
    }
}
