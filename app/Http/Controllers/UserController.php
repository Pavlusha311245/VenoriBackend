<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
}
