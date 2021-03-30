<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\Fluent\Concerns\Has;


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
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:8'
        ]);

        $validData['password'] = bcrypt($validData['password']);

        $user = User::create($validData);

        return response()->json(['message' => 'You were successfully registered. Use your email and password to sign in.', 'user' => $user], 201);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postLogin(Request $request): JsonResponse
    {
        $loginData = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:8'
        ]);

        if (!auth()->attempt($loginData)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return response()->json(['user' => auth()->user(), 'access_token' => $accessToken], 200);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function postForgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);
        $email = $request->input('email');
        $user = User::whereEmail($email)->first();
        $token = Hash::make($user->email . now());
        $userToken = DB::table('password_resets')->where('email', $email)->first();

        if ($userToken == null)
            DB::table('password_resets')->insert(['email' => $email, 'token' => $token]);
        else
            DB::table('password_resets')->update(['token' => $token]);

        Mail::send('password.forgot', ['token' => $token],
            function (Message $message) use ($email) {
                $message->to($email)->subject('Reset your password');
            });

        return response(['message' => 'Check your email'], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function postResetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);

        $token = $request->input('token');
        if (!$passwordResets = DB::table('password_resets')->where('token', $token)->first())
            return response(['message' => 'Invalid token'], 400);

        if (!$user = User::where('email', $passwordResets->email)->first())
            return response(['message' => 'User does not exist'], 404);

        $user->password = Hash::make($request->input('password'));
        $user->save();
        return response([
            'message' => 'Success change password'
        ]);
    }
}
