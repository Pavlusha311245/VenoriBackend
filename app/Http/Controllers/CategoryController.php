<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Place;
use App\Services\ImageService;
use App\Services\PaginateArrayService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

/**
 * Class CategoryController for CRUD Categories
 * @package App\Http\Controllers
 */
class CategoryController extends Controller
{
    protected $imageService;
    protected $arrayPaginator;

    public function __construct(ImageService $imageService, PaginateArrayService $paginateArrayService)
    {
        $this->imageService = $imageService;
        $this->arrayPaginator = $paginateArrayService;
    }

    /**
     * @OA\Get(
     *     path="/api/categories",
     *     summary="Category info",
     *     description="Getting a list of all categories",
     *     operationId="categoriesIndex",
     *     tags={"categories"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success getting a list of all categories",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Category")
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
        return Category::paginate(Config::get('constants.pagination.count'));
    }

    /**
     * @OA\Post(
     *     path="/api/categories",
     *     summary="Adds a new category",
     *     description="Adds a new category",
     *     operationId="categoryStore",
     *     tags={"categories"},
     *     security={ {"bearer": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass data to add a new category",
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", maxLength=255, example="Coffee"),
     *              @OA\Property(property="image", type="file", maxLength=255, example="(file path)")
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success creating category",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/Category")
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
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
     *                      property="name",
     *                      type="array",
     *                      @OA\Items(type="string", example="The name field is required.")
     *                  )
     *              )
     *          )
     *      ),
     * )
     */
    public function store(Request $request)
    {
        $validateCategory = $request->validate([
            'name' => 'required|string|unique:categories|max:255',
            'image' => 'required|image|mimes:jpg,png'
        ]);

        $url = $this->imageService->upload($validateCategory['image'], 'CategoryImages');

        $validateCategory['image_url'] = $url;

        $category = Category::create($validateCategory);

        return response($category, 201);
    }

    /**
     * Display a category by ID
     *
     * @param $id
     * @return Response|string
     */
    public function show($id)
    {
        return Category::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/categories/{id}",
     *     summary="Updates the category",
     *     description="Updates the category",
     *     operationId="categoriesUpdate",
     *     tags={"categories"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of category",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *          required=false,
     *          description="Pass data to add a new category",
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", maxLength=255, example="Coffee"),
     *              @OA\Property(property="image", type="file", maxLength=255, example="(file path)")
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success updating category information",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/Category")
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
     *          description="Category not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No category found")
     *          )
     *     )
     * )
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'string',
            'image_url' => 'string'
        ]);

        $category->update($request->all());

        return response()->json($category);
    }

    /**
     * @OA\Post(
     *     path="/api/categories/{id}/uploadImage",
     *     summary="Updates the category picture",
     *     description="Updates the category picture",
     *     operationId="categoryUploadImage",
     *     tags={"categories"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of category",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *          required=false,
     *          description="Pass data to add a new category image",
     *          @OA\JsonContent(
     *              @OA\Property(property="image", type="file", maxLength=255, example="(file path)")
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success storing a new user",
     *          @OA\JsonContent(
     *              @OA\Property(property="image_url", type="string", maxLength=255, example="storage/CategoryImages/236095676.png")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Category not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No category found")
     *          )
     *     ),
     * )
     */
    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,png'
        ]);

        $url = $this->imageService->upload($request->file('image'), 'CategoryImages');

        $category = Category::findOrFail($id);
        $category->update(['image_url' => $url]);

        return response()->json(['image_url' => $url]);
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{id}",
     *     summary="Deleting category",
     *     description="Deleting category",
     *     operationId="categoriesDelete",
     *     tags={"categories"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of category",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success deleting category",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Category is deleted successfully")
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
     *          description="Category not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No category found")
     *          )
     *     ),
     * )
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->places()->detach();
        $category->delete();

        $this->imageService->delete($category->image_url);

        return response()->json(['message' => 'Category is deleted successfully']);
    }

    /**
     * @OA\Get(
     *     path="/api/categories/{id}/places",
     *     summary="Category info",
     *     description="Getting a list of all places by category",
     *     operationId="categoriesGetPlaces",
     *     tags={"categories"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of category",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success getting a list of all places by category",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Place")
     *              )
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
     *          description="Category not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No category found")
     *          )
     *     )
     * )
     */
    public function getPlaces($id)
    {
        $places = Category::findOrFail($id)->places()->get();

        foreach ($places as $place)
            $place['favourite'] = auth()->user()->favoutirePlaces()->find($place->id) !== null;

        return response()->json($this->arrayPaginator->paginate($places, Config::get('constants.pagination.count')));
    }

    /**
     * @OA\Post(
     *     path="/api/categories/{category_id}/place/{place_id}",
     *     summary="Adds a new place in selected category",
     *     description="Adds a new place in selected category",
     *     operationId="categoryAddPlace",
     *     tags={"categories"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of category",
     *          in="path",
     *          name="category_id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Parameter(
     *          description="ID of place",
     *          in="path",
     *          name="place_id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success added place to category",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/Place")
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Place exist in selected category",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The place has already been assigned the selected category"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Place not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No place found")
     *          )
     *     )
     * )
     */
    public function addCategoryForPlace($category_id, $place_id)
    {
        $category = Category::findOrFail($category_id);

        if ($category->places()->find($place_id) !== null)
            return response()->json(['message' => 'The place has already been assigned the selected category'], 400);

        $category->places()->attach($place_id);

        return response()->json(Place::findOrFail($place_id), 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/categories/{category_id}/place/{place_id}",
     *     summary="Remove place from category",
     *     description="Remove place from category",
     *     operationId="categoriesDeletePlace",
     *     tags={"categories"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of category",
     *          in="path",
     *          name="category_id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Parameter(
     *          description="ID of place",
     *          in="path",
     *          name="place_id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success deleting place from category",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Place successfully removed in selected category")
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
     *          description="Category not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No category found")
     *          )
     *     )
     * )
     */
    public function removePlaceFromCategory($category_id, $place_id)
    {
        $category = Category::findOrFail($category_id);

        throw_if($category->places()->find($place_id) === null, new ModelNotFoundException('Place does not exist in selected category'));

        $category->places()->detach($place_id);

        return response()->json(['message' => 'Place successfully removed in selected category']);
    }
}
