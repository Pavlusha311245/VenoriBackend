<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postRegistration(Request $request): JsonResponse
    {
        $validData = $request->validate([
            'first_name' => 'required|min:2',
            'second_name' => 'required|min:2',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8'
        ]);
        $validData['password'] = bcrypt($validData['password']);

        $user = User::create($validData);

        return response()->json(['message' => 'You were successfully registered. Use your email and password to sign in.', 'user' => $user], 201);
    }

    public function postLogin(Request $request) {
        $loginData = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return response()->json(['user' => auth()->user(), 'access_token' => $accessToken], 200);
    }
}
