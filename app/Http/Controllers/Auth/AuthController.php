<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailJob;
use App\Mail\LoginMail;
use App\Mail\RegisterMail;
use App\Mail\VenoriMail;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

/**
 * Class AuthController
 * @package App\Http\Controllers\Auth
 */
class AuthController extends Controller
{
    /**
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
     *              @OA\Property(property="password", type="string", format="password", example="PassWord12345")
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success registration a new user",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="first_name", type="string", example="Jack"),
     *              @OA\Property(property="second_name", type="string", example="Smith"),
     *              @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *              @OA\Property(property="created_at", type="string", format="date-time", example="2019-02-25 12:59:20"),
     *              @OA\Property(property="updated_at", type="string", format="date-time", example="2019-02-25 12:59:20"),
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(
     *                  property="roles",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="User"),
     *                      @OA\Property(property="guard_name", type="string", example="web"),
     *                      @OA\Property(property="created_at", type="string", format="date-time", example="2019-02-25 12:59:20"),
     *                      @OA\Property(property="updated_at", type="string", format="date-time", example="2019-02-25 12:59:20"),
     *                      @OA\Property(
     *                          property="pivot",
     *                          type="object",
     *                          @OA\Property(property="model_id", type="integer", example=3),
     *                          @OA\Property(property="role_id", type="string", example=1),
     *                          @OA\Property(property="model_type", type="string", example="App\\Models\\User"),
     *                      ),
     *                  ),
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
     *                      @OA\Items(type="string", example="The first name field is required.")
     *                  ),
     *                  @OA\Property(
     *                      property="email",
     *                      type="array",
     *                      @OA\Items(type="string", example="The email has already been taken.")
     *                  )
     *              )
     *          )
     *      )
     * )
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
        $role = Role::findByName('User');
        $user->assignRole($role);

        SendEmailJob::dispatch(['user' => $user, 'mail' => new VenoriMail(['user' => $user, 'view' => 'mail.register'])]);

        return response()->json($user, 201);
    }

    /**
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
     *              @OA\Property(property="password", type="string", format="password", example="PassWord12345")
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success login the user",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5MzMxOTY3My0yNTkxLTRlNGMtOWUwMy02MzRlMDYzNWUzMDMiLCJqdGkiOiI5MjUzMTU1YjY0MWIwMWY3NDEwMGNmMjhhOGIxZmU2NzNhZjhjMGExZDM1MGFiNDdmN2M3ZTRlNTNjNDUxYmIyMDUzMTAyNDRmNWExZjdmYyIsImlhdCI6MTYxODQ4MDgzOS4zNDY0ODYsIm5iZiI6MTYxODQ4MDgzOS4zNDY0OTgsImV4cCI6MTY1MDAxNjgzOS4zMzAxOTEsInN1YiI6IjEiLCJzY29wZXMiOltdfQ.FoOSs7tl3hfN1KWG40WexLGG67qsZ6SYzVUX_zAAu5H-AaxNv-p-dmY8Q6zh2HnHwPbVKrjTXLVM28nqXSanOreP2Tpfwq_LbrjAYLuEpzrY17QMddtbQ3iXh7M7OX0AyRFcw6Z4_SZVwc-sfUK-rcTyIo9e7XHtCKBsE4yaVmFgHX933s6ltmZzk5bzLP993WrMOlvuqxFHcVV6STQzuFb4QC7qkw3Pv62p-E7WTaoHbWhR5EM5FsxXSDai_zjHVmRFpIdPmrfE9eilcrTYQP-OcsbR37rKdYTdoHnaTxO3P6KVDehSsI1TU0Uok6K_8liTZU32cxV8s1nOUuM74z_d-k-chLbcm1-hKAVn5SyAKWrXbs2AI8WgoWV4msZI_VQX0lp9C8Nx12zhWDOe4AGff8gJAB7OkQIT4FMt1UIdc_04lASbU8YJV2Oht-DOwnk-G71_uUdc4REgkBf29IggdQojuXLvxFzvF3ORd3rzPQ9xGGlrV5h2UBgZd039qnqRrmZJah36oC0OgHaRGqzmQzenTAeKxyBM7zH5tsj5nwSU7cUvHq2v15XLqd7JHKuK2dPa0AJGCwUcejSs6WSTjUxbuq8Zfc76WR-6G0pv7gnvMlD0CgLR4o6sgXRtVO74Q2nGmxV49cnGc45zCI0Q_CifRD2cagZyE0USMdM"),
     *              @OA\Property(property="user", type="object", ref="#/components/schemas/User")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthorized")
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
     *                      @OA\Items(type="string", example="The email field is required.")
     *                  ),
     *                  @OA\Property(
     *                      property="password",
     *                      type="array",
     *                      @OA\Items(type="string", example="The password field is required.")
     *                  )
     *              )
     *          )
     *      )
     * )
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

        SendEmailJob::dispatch(['user' => auth()->user(), 'mail' => new VenoriMail(['view' => 'mail.login'])]);

        return response()->json(['access_token' => $accessToken, 'user' => auth()->user()]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function loginAdmin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|min:8'
        ]);

        if (auth()->attempt($request->only(['email', 'password']))) {
            if (auth()->user()->hasRole('User')) {
                auth()->logout();
                return redirect('/login')->withErrors([
                    'error' => 'You don\'t have access'
                ]);
            } else
                return redirect('/')->with('success', 'Success login');
        }


        return redirect('/login')->withErrors([
            'error' => 'Invalid login or password'
        ]);
    }

    /**
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
     *              @OA\Property(property="email", type="string", format="email", example="user1@mail.com")
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success sending password recovery email the user",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string", format="email", example="user1@mail.com")
     *          )
     *     )
     * )
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

        SendEmailJob::dispatch(['user' => $user, 'mail' => new VenoriMail(['view' => 'mail.forgot', 'token' => $token])]);

        return response()->json(['email' => $email]);
    }

    /**
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
     * )
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required'
        ]);
        $token = $request->input('token');

        $passwordResets = DB::table('password_resets')->where('token', $token);

        if (!$passwordResets->first())
            return response(['message' => 'Invalid token'], 400);

        $user = User::where('email', $passwordResets->first()->email)->first();
        $user->password = Hash::make($request->input('password'));
        $user->save();

        $passwordResets->delete();

        return response()->json(['message' => 'Success change password']);
    }

    /**
     * @OA\Post(
     *     path="/api/user/resetPassword",
     *     summary="Reset password",
     *     description="Changing authorized user password",
     *     operationId="reset",
     *     tags={"authentication"},
     *     @OA\Response(
     *          response=200,
     *          description="Success changing authorized user password",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="Success change password")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthenticated.")
     *          )
     *     )
     * )
     */
    public function resetPasswordAuthUser(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8'
        ]);

        auth()->user()->password = Hash::make($request->get('password'));
        auth()->user()->save();

        return response()->json(['message' => 'Success change password']);
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function resetPasswordView(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed'
        ]);

        auth()->user()->password = Hash::make($request->input('password'));
        auth()->user()->save();

        return redirect('/admin/user/resetPassword')->with(['message' => 'Success change password']);
    }

    /**
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
     *     ),
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Logout successful']);
    }
}
