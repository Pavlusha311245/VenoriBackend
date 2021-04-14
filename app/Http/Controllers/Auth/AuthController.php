<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearer",
 *     type="http",
 *     scheme="bearer"
 * )
 * @OA\Post(
 *     path="/api/register",
 *     summary="Registration",
 *     description="Registration of a new user by first_name, second_name, email, password",
 *     operationId="registration",
 *     tags={"auth"},
 *     @OA\RequestBody(
 *          required=true,
 *          description="Pass data to register a new user",
 *          @OA\JsonContent(
 *              required={"first_name","second_name","email","password"},
 *              @OA\Property(property="first_name", type="string", example="Jack"),
 *              @OA\Property(property="second_name", type="string", example="Smith"),
 *              @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *              @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
 *          )
 *     ),
 *     @OA\Response(
 *          response=201,
 *          description="Success registration a new user",
 *          @OA\Items(
 *              type="object",
 *              @OA\Property(property="first_name", type="string", example="The second name field is required"),
 *          )
 *     ),
 *     @OA\Response(
 *          response=422,
 *          description="Wrong data to register a new user",
 *     @OA\JsonContent(
 *          @OA\Property(property="message", type="string", example="The given data was invalid."),
 *          @OA\MediaType(
 *              mediaType="application/json",
 *              @OA\Schema(
 *                  type="array",
 *                  @OA\Items(
 *                      type="object",
 *                      @OA\Property(
 *                          property="first_name",
 *                          type="string",
 *                          example="The second name field is required."
 *                      ),
 *                      @OA\Property(
 *                      property="first_name",
 *                      type="string",
 *                      example="The second name field is required."
 *                  ),
 *                  @OA\Property(
 *                      property="first_name",
 *                      type="string",
 *                      example="The second name field is required."
 *                  ),
 *          )
 *     )
 * )
 */
class AuthController extends Controller
{
    /**
     * Method for user registration
     *
     * @param  [string] first_name
     * @param  [string] second_name
     * @param  [string] email
     * @param  [string] password
     * @return [json] user object
     */
    public function register(Request $request)
    {
        $validData = $request->validate([
            'first_name' => 'required|min:2',
            'second_name' => 'required|min:2',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:8'
        ]);
        $validData['password'] = bcrypt($validData['password']);

        $user = User::create($validData);

        return response()->json($user, 201);
    }

    /**
     * Method for authorizing a user and issuing a token to him
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:8'
        ]);
        if (!auth()->attempt($loginData))
            return response()->json(['error' => 'Unauthorized'], 401);

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response()->json(['access_token' => $accessToken, 'user' => auth()->user()]);
    }

    /**
     * Method for sending password recovery email
     * @param Request $request
     * @return JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);
        $email = $request->input('email');

        $user = User::whereEmail($email)->first();
        $token = Hash::make($user->email . now());

        DB::table('password_resets')->updateOrInsert(['email' => $email], ['token' => $token]);

        Mail::send('password.forgot', ['token' => $token],
            function (Message $message) use ($email) {
                $message->to($email)->subject('Reset your password');
            });

        return response()->json($email, 200);
    }

    /**
     * Method for changing the password if there is a token
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function resetPassword(Request $request)
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

        return response()->json(['message' => 'Success change password']);
    }

    /**
     * Method for logging out a user from an account
     * @param Request $request
     * @return Application|ResponseFactory|JsonResponse|Response
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Logout successful']);
    }
}
