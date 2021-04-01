<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class UserController extends Controller
{
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

    public function getUserLocation(Request $request, $id)
    {
        $userIp = \request()->ip();
        $geoInfoJSON = json_decode(file_get_contents("http://ip-api.com/json/$userIp?lang=http://ip-api.com/json/$userIp?fields=countryCode"), true);
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
        return response($user, 200);
    }
}
