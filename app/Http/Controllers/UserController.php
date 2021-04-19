<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImageService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * @OA\SecurityScheme(
     *   securityScheme="api_key",
     *   type="apiKey",
     *   in="header",
     *   name="api_key"
     * ),
     * @OA\Get(
     *     path="/api/users",
     *     summary="Users info",
     *     description="Getting a list of all users",
     *     operationId="usersIndex",
     *     tags={"users"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success getting a list of all users",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      ref="#/components/schemas/User"
     *                  ),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthorized"),
     *          )
     *     )
     * )
     */
    public function index()
    {
        return User::paginate(5);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Add user",
     *     description="Adding a new user",
     *     operationId="usersStore",
     *     tags={"users"},
     *     security={ {"bearer": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass data to add a new user",
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
     *          description="Success storing a new user",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="first_name", type="string", example="Jack"),
     *              @OA\Property(property="second_name", type="string", example="Smith"),
     *              @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *              @OA\Property(property="created_at", type="string", format="date-time", example="2019-02-25 12:59:20"),
     *              @OA\Property(property="updated_at", type="string", format="date-time", example="2019-02-25 12:59:20"),
     *              @OA\Property(property="id", type="integer", example=1),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
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
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|min:2',
            'second_name' => 'required|min:2',
            'email' => 'required|email|unique:users|max:255',
            'address_full' => 'string',
            'address_lat' => 'double',
            'address_lon' => 'double',
            'avatar' => 'string',
            'password' => 'required|min:8',
        ]);

        $user = User::create($request->all());

        return response()->json($user, 201);
    }

    /**
     * The method returns information about user
     * @return JsonResponse
     */
    public function showProfile()
    {
        return User::findOrFail(auth()->user()->id);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Update user",
     *     description="Updating user information",
     *     operationId="usersUpdate",
     *     tags={"users"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of user",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass data to update user information",
     *          @OA\JsonContent(
     *              @OA\Property(property="first_name", type="string", maxLength=255, example="John"),
     *              @OA\Property(property="second_name", type="string", maxLength=255, example="Doe"),
     *              @OA\Property(property="email", type="string", format="email", description="User unique email address", example="user@gmail.com"),
     *              @OA\Property(property="address_full", type="string", maxLength=255, example="Minsk"),
     *              @OA\Property(property="address_lat", type="number", example="53.913224"),
     *              @OA\Property(property="address_lon", type="number", example="27.467663"),
     *              @OA\Property(property="email_verified_at", type="string", readOnly=true, format="date-time", description="Datetime marker of verification status", example="2019-02-25 12:59:20"),
     *              @OA\Property(property="password", type="string", maxLength=255, example="Passwo424hg"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success updating user information",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="first_name", type="string", maxLength=255, example="John"),
     *              @OA\Property(property="second_name", type="string", maxLength=255, example="Doe"),
     *              @OA\Property(property="email", type="string", format="email", description="User unique email address", example="user@gmail.com"),
     *              @OA\Property(property="address_full", type="string", maxLength=255, example="Minsk"),
     *              @OA\Property(property="address_lat", type="number", example="53.913224"),
     *              @OA\Property(property="address_lon", type="number", example="27.467663"),
     *              @OA\Property(property="avatar", type="string", example="storage/PlaceImages/KFC.png"),
     *              @OA\Property(property="email_verified_at", type="string", readOnly=true, format="date-time", description="Datetime marker of verification status", example="2019-02-25 12:59:20"),
     *              @OA\Property(property="created_at", type="string", format="date-time", example="2021-04-15T12:37:21.000000Z"),
     *              @OA\Property(property="updated_at", type="string", format="date-time", example="2021-04-15T13:07:18.000000Z")
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
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
     *                          example="The email has already been taken.",
     *                      )
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'min:2',
            'second_name' => 'min:2',
            'email' => 'max:255|email|unique:users',
            'address_full' => 'string',
            'address_lat' => 'double',
            'address_lon' => 'double',
            'avatar' => 'file'
        ]);

        $user->update($request->all());

        return response()->json($user, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/user/{id}/uploadAvatar",
     *     summary="Upload user avatar",
     *     description="Uploading avatar for user",
     *     operationId="usersAvatar",
     *     tags={"users"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of user",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *     ),
     *     @OA\Parameter(
     *          description="Image for user",
     *          in="path",
     *          name="avatar",
     *          required=true,
     *          @OA\Schema(
     *              type="file",
     *              format="file"
     *         )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success uploading avatar for user",
     *          @OA\Property(
     *              @OA\Property(type="string", example="storage/UserAvatars/589373154.png"),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="object", example="Unauthorized"),
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
     *                      property="image",
     *                      type="array",
     *                      @OA\Items(
     *                          type="string",
     *                          example="The image name field is required.",
     *                      )
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function uploadAvatar(Request $request, $id)
    {
        $imageService = new ImageService;

        $request->validate([
            'image' => 'required|image|mimes:jpg,png'
        ]);

        $url = $imageService->upload($request->file('image'), 'UserAvatars');

        $user = User::findOrFail($id);

        $user->update(['avatar' => $url]);

        return response()->json($url, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Delete user",
     *     description="Deleting user",
     *     operationId="usersDelete",
     *     tags={"users"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of user",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success deleting user",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User is deleted successfully")
     *          ),
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="ModelNotFoundException handled for API")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthorized"),
     *          )
     *     )
     * )
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User is deleted successfully'], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/user/location",
     *     summary="Location of user",
     *     description="Getting location of user",
     *     operationId="usersLocation",
     *     tags={"users"},
     *     security={ {"bearer": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass data to getting location of user",
     *          @OA\JsonContent(
     *              @OA\Property(property="address_full", type="string", maxLength=255, example="Minsk"),
     *              @OA\Property(property="address_lat", type="number", example="53.913224"),
     *              @OA\Property(property="address_lon", type="number", example="27.467663"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success getting location of user",
     *          @OA\JsonContent(
     *              @OA\Property(property="address_full", type="string", maxLength=255, example="Minsk"),
     *              @OA\Property(property="address_lat", type="number", example="53.913224"),
     *              @OA\Property(property="address_lon", type="number", example="27.467663"),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthorized",
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
     *                      property="address_full",
     *                      type="array",
     *                      @OA\Items(
     *                          type="string",
     *                          example="The first name field is required.",
     *                      )
     *                  ),
     *              )
     *          )
     *      )
     * )
     */
    public function location(Request $request)
    {
        $userLocation = $request->validate([
            'address_full' => 'required|string',
            'address_lat' => 'required|numeric',
            'address_lon' => 'required|numeric',
        ]);

        $user = User::findOrFail(auth()->id());
        $user->update($userLocation);
        $user->save();

        return response()->json($userLocation, 200);
    }
}
