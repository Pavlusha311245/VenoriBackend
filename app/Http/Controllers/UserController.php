<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        return response($users,200);
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
            'password' => 'required|min:8'
        ]);

        $user = User::create($request->all());
        return response($user, 201);
    }

    /**
     * @param int $id
     * @return Application|ResponseFactory|Response
     */
    public function show(int $id)
    {
        try {
            return User::findOrFail(Auth::id());
        } catch (ModelNotFoundException $exception) {
            return response(['message' => 'User Is Not Found'], 201);
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
            'password' => 'min:8'
        ]);

        $user->update($request->all());
        return response(['message' => 'User is updated successfully'], 201);
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
            return response(['message' => 'User is deleted successfully'], 200);
        } catch (ModelNotFoundException $ex) {
            return response(['error' => 'User not found'], 404);
        }
    }
}
