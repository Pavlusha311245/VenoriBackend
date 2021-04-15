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
 *     tags={"authentication"},
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
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(
 *                  property="first_name",
 *                  type="string",
 *                  example="Jack"
 *              ),
 *              @OA\Property(
 *                  property="second_name",
 *                  type="string",
 *                  example="Smith"
 *              ),
 *              @OA\Property(
 *                  property="email",
 *                  type="string",
 *                  format="email",
 *                  example="user1@mail.com"
 *              ),
 *              @OA\Property(
 *                  property="created_at",
 *                  type="string",
 *                  format="date-time",
 *                  example="2019-02-25 12:59:20"
 *              ),
 *              @OA\Property(
 *                  property="updated_at",
 *                  type="string",
 *                  format="date-time",
 *                  example="2019-02-25 12:59:20"
 *              ),
 *              @OA\Property(
 *                  property="id",
 *                  type="integer",
 *                  example=1
 *              ),
 *          ),
 *     ),
 *     @OA\Response(
 *          response=422,
 *          description="Validation error",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="The given data was invalid."),
 *              @OA\Property(
 *                  property="errors",
 *                  type="object",
 *                  @OA\Property(
 *                      property="first_name",
 *                      type="array",
 *                      @OA\Items(
 *                          type="string",
 *                          example="The first name field is required.",
 *                      )
 *                  ),
 *                  @OA\Property(
 *                      property="email",
 *                      type="array",
 *                      @OA\Items(
 *                          type="string",
 *                          example="The email has already been taken.",
 *                      )
 *                  )
 *              )
 *          )
 *      )
 * ),
 * @OA\Post(
 *     path="/api/login",
 *     summary="Login",
 *     description="Login of the user by email, password",
 *     operationId="login",
 *     tags={"authentication"},
 *     @OA\RequestBody(
 *          required=true,
 *          description="Pass data to login the user",
 *          @OA\JsonContent(
 *              required={"email","password"},
 *              @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *              @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
 *          )
 *     ),
 *     @OA\Response(
 *          response=200,
 *          description="Success login the user",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5MzMxOTY3My0yNTkxLTRlNGMtOWUwMy02MzRlMDYzNWUzMDMiLCJqdGkiOiI5MjUzMTU1YjY0MWIwMWY3NDEwMGNmMjhhOGIxZmU2NzNhZjhjMGExZDM1MGFiNDdmN2M3ZTRlNTNjNDUxYmIyMDUzMTAyNDRmNWExZjdmYyIsImlhdCI6MTYxODQ4MDgzOS4zNDY0ODYsIm5iZiI6MTYxODQ4MDgzOS4zNDY0OTgsImV4cCI6MTY1MDAxNjgzOS4zMzAxOTEsInN1YiI6IjEiLCJzY29wZXMiOltdfQ.FoOSs7tl3hfN1KWG40WexLGG67qsZ6SYzVUX_zAAu5H-AaxNv-p-dmY8Q6zh2HnHwPbVKrjTXLVM28nqXSanOreP2Tpfwq_LbrjAYLuEpzrY17QMddtbQ3iXh7M7OX0AyRFcw6Z4_SZVwc-sfUK-rcTyIo9e7XHtCKBsE4yaVmFgHX933s6ltmZzk5bzLP993WrMOlvuqxFHcVV6STQzuFb4QC7qkw3Pv62p-E7WTaoHbWhR5EM5FsxXSDai_zjHVmRFpIdPmrfE9eilcrTYQP-OcsbR37rKdYTdoHnaTxO3P6KVDehSsI1TU0Uok6K_8liTZU32cxV8s1nOUuM74z_d-k-chLbcm1-hKAVn5SyAKWrXbs2AI8WgoWV4msZI_VQX0lp9C8Nx12zhWDOe4AGff8gJAB7OkQIT4FMt1UIdc_04lASbU8YJV2Oht-DOwnk-G71_uUdc4REgkBf29IggdQojuXLvxFzvF3ORd3rzPQ9xGGlrV5h2UBgZd039qnqRrmZJah36oC0OgHaRGqzmQzenTAeKxyBM7zH5tsj5nwSU7cUvHq2v15XLqd7JHKuK2dPa0AJGCwUcejSs6WSTjUxbuq8Zfc76WR-6G0pv7gnvMlD0CgLR4o6sgXRtVO74Q2nGmxV49cnGc45zCI0Q_CifRD2cagZyE0USMdM"),
 *              @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
 *          ),
 *     ),
 *     @OA\Response(
 *          response=401,
 *          description="Validation error",
 *          @OA\JsonContent(
 *              @OA\Property(property="error", type="string", example="Unauthorized"),
 *          )
 *     ),
 *     @OA\Response(
 *          response=422,
 *          description="Validation error",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="The given data was invalid."),
 *              @OA\Property(
 *                  property="errors",
 *                  type="object",
 *                  @OA\Property(
 *                      property="email",
 *                      type="array",
 *                      @OA\Items(
 *                          type="string",
 *                          example="The email field is required.",
 *                      )
 *                  ),
 *                  @OA\Property(
 *                      property="password",
 *                      type="array",
 *                      @OA\Items(
 *                          type="string",
 *                          example="The password field is required.",
 *                      )
 *                  )
 *              )
 *          )
 *      )
 * ),
 * @OA\Post(
 *     path="/api/forgot",
 *     summary="Forgot password",
 *     description="Sending password recovery email",
 *     operationId="forgot",
 *     tags={"authentication"},
 *     @OA\RequestBody(
 *          required=true,
 *          description="Pass data to send a password recovery email",
 *          @OA\JsonContent(
 *              required={"email"},
 *              @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *          )
 *     ),
 *     @OA\Response(
 *          response=200,
 *          description="Success sending password recovery email the user",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="email", type="string", format="email", example="user1@mail.com")
 *          ),
 *     )
 * ),
 * @OA\Post(
 *     path="/api/reset",
 *     summary="Reset password",
 *     description="Changing the password if there is a token",
 *     operationId="reset",
 *     tags={"authentication"},
 *     @OA\Response(
 *          response=200,
 *          description="Success changing the password if there is a token",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="message", type="string", example="Success change password")
 *          )
 *     ),
 *     @OA\Response(
 *          response=400,
 *          description="Validation error",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="message", type="string", example="Invalid token")
 *          )
 *     )
 * ),
 * @OA\Post(
 *     path="/api/logout",
 *     summary="Logout",
 *     description="Logging out a user from an account",
 *     operationId="logout",
 *     tags={"authentication"},
 *     security={ {"bearer": {} }},
 *     @OA\Response(
 *          response=200,
 *          description="Success logging out a user from an account",
 *          @OA\JsonContent(
 *              type="object",
 *              @OA\Property(property="message", type="string", example="Logout successful")
 *          )
 *     )
 * )
 */
class AuthController extends Controller
{
    /**
     * Method for user registration
     *
     * @param Request $request
     * @return JsonResponse
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
