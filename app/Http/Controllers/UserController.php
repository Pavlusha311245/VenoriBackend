<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use App\Services\ImageService;
use Illuminate\Http\Request;
use League\Flysystem\Config;
use Spatie\Permission\Models\Role;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

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
     *                  @OA\Items(type="object", ref="#/components/schemas/User")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     )
     * )
     */
    public function index()
    {
        return User::paginate(5);
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function create(Request $request)
    {
        $validData = $request->validate([
            'first_name' => 'required|min:2',
            'second_name' => 'required|min:2',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:8',
            'role' => 'required|string'
        ]);
        $validData['password'] = bcrypt($validData['password']);

        $roleName = $request->get('role');
        $user = User::create($validData);
        $role = Role::findByName($roleName);
        $user->assignRole($role);

        return redirect('/admin/users')->with('message', 'Successful registration');
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function edit(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'min:2|string',
            'second_name' => 'min:2|string',
            'email' => 'required',
            'role' => 'required'
        ]);

        $userRoles = $request->get('role');
        $user = User::findOrFail($id);
        $user->update($request->all());
        $user->syncRoles($userRoles);
        $user->save();

        return redirect("/admin/users/$id")->with('message', 'User was updated');
    }

    /**
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function remove($id)
    {
        $user = User::findOrFail($id);
        $user->favoutirePlaces()->detach();
        $user->orders()->delete();
        $user->delete();

        if ($user->avatar !== 'storage/UserAvatars/defaultAvatar.png')
            $this->imageService->delete($user->avatar);

        return redirect("/admin/users/")->with('message', 'User was deleted');
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
     *              @OA\Property(property="password", type="string", format="password", example="PassWord12345")
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
     *              @OA\Property(property="id", type="integer", example=1)
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
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
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|min:2',
            'second_name' => 'required|min:2',
            'email' => 'required|email|unique:users|max:255',
            'address_full' => 'required|string',
            'address_lat' => 'required|numeric',
            'address_lon' => 'required|numeric',
            'password' => 'required|min:8',
            'role' => 'string'
        ]);

        $roleName = $request->get('role');
        $user = User::create($request->all());
        $role = Role::findByName($roleName);
        $user->assignRole($role);

        return response()->json($user, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Show user",
     *     description="Showing user by Id",
     *     operationId="usersShow",
     *     tags={"users"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of user",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success showing user",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/User")
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
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No user found")
     *          )
     *     )
     * )
     */
    public function show($id)
    {
        return User::findOrFail($id);
    }

    /**
     * @OA\Get(
     *     path="/api/user/details",
     *     summary="Show user",
     *     description="Showing auth user",
     *     operationId="usersShowProfile",
     *     tags={"users"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success showing user",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No user found")
     *          )
     *     )
     * )
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
     *          @OA\Schema(type="integer", format="int64")
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
     *              @OA\Property(property="password", type="string", maxLength=255, example="Passwo424hg")
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
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No user found")
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
     *                      @OA\Items(type="string", example="The email has already been taken.")
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
            'address_full' => 'required|string',
            'address_lat' => 'required|numeric',
            'address_lon' => 'required|numeric',
            'avatar' => 'file'
        ]);

        $user->update($request->all());

        return response()->json($user);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{id}/uploadAvatar",
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
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Parameter(
     *          description="Image for user",
     *          in="path",
     *          name="avatar",
     *          required=true,
     *          @OA\Schema(type="file", format="file")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success uploading avatar for user",
     *          @OA\JsonContent(
     *              @OA\Property(property="image_url", type="string", maxLength=255, example="storage/UsersAvatars/236095676.png")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="object", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No user found")
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
     *                      @OA\Items(type="string", example="The image name field is required.")
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function uploadAvatar(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,png'
        ]);

        $imageService = new ImageService;

        $url = $imageService->upload($request->file('image'), 'UsersAvatars');

        $user = User::findOrFail($id);
        $user->update(['avatar' => $url]);

        return response()->json(['image_url' => $url]);
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
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success deleting user",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User is deleted successfully")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No user found")
     *          )
     *     )
     * )
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->favoutirePlaces()->detach();
        $user->delete();

        if ($user->avatar !== 'storage/UserAvatars/defaultAvatar.png')
            $this->imageService->delete($user->avatar);

        return response()->json(['message' => 'User is deleted successfully']);
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
     *              @OA\Property(property="address_lon", type="number", example="27.467663")
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success getting location of user",
     *          @OA\JsonContent(
     *              @OA\Property(property="address_full", type="string", maxLength=255, example="Minsk"),
     *              @OA\Property(property="address_lat", type="number", example="53.913224"),
     *              @OA\Property(property="address_lon", type="number", example="27.467663")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
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
     *                      @OA\Items(type="string", example="The first name field is required.")
     *                  )
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

        $user = auth()->user();
        $user->update($userLocation);

        return response()->json($userLocation);
    }

    /**
     * @OA\Get(
     *     path="/api/user/reviews",
     *     summary="Get user reviews",
     *     description="Getting auth user reviews",
     *     operationId="reviewsAuthUser",
     *     tags={"users"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success getting a auth user reviews",
     *          @OA\JsonContent(
     *              @OA\Items(ref="#/components/schemas/Review")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No user found")
     *          )
     *     )
     * )
     */
    public function getReviews()
    {
        return auth()->user()->reviews()->paginate(Config::get('constants.paginate.count'));
    }
}
