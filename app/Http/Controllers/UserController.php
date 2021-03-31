<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * @param $id
     * @return JsonResponse
     */
    public function showProfile($id)
    {
        try {
            return User::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'User Is Not Found'], 201);
        }
    }
}
