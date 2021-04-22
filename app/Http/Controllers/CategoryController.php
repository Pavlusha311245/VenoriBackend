<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\ImageService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class CategoryController for CRUD Categories
 * @package App\Http\Controllers
 */
class CategoryController extends Controller
{
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
     *                  @OA\Items(
     *                      type="object",
     *                      ref="#/components/schemas/Category"
     *                  ),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *     ),
     * )
     */
    public function index()
    {
        return Category::paginate(5);
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
     *              @OA\Property(property="image", type="file", maxLength=255, example="(file path)"),
     *     )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success creating category",
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/Category"
     *          ),
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Category not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="ModelNotFoundException handled for API")
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
     *                      @OA\Items(
     *                          type="string",
     *                          example="The name field is required.",
     *                      )
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $imageService = new ImageService;

        $request->validate([
            'image' => 'required|image|mimes:jpg,png'
        ]);

        $url = $imageService->upload($request->file('image'), 'CategoryImages');

        $request->validate([
            'name' => 'string',
            'image_url' => $url
        ]);

        $category = Category::create([
            'image_url' => $url,
            'name' => $request->name
        ]);

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
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *     ),
     *     @OA\RequestBody(
     *          required=false,
     *          description="Pass data to add a new category",
     *          @OA\JsonContent(
     *              @OA\Property(property="name", type="string", maxLength=255, example="Coffee"),
     *              @OA\Property(property="image", type="file", maxLength=255, example="(file path)"),
     *     )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success updating category information",
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/Category"
     *          ),
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Category not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="ModelNotFoundException handled for API")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *     ),
     *      )
     * )
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'string',
            'image_url' => 'string'
        ]);

        $category->update($request->all());

        return response()->json($category, 200);
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
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *     ),
     *     @OA\RequestBody(
     *          required=false,
     *          description="Pass data to add a new category image",
     *          @OA\JsonContent(
     *              @OA\Property(property="image", type="file", maxLength=255, example="(file path)"),
     *     )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success storing a new user",
     *          @OA\JsonContent(
     *              @OA\Property(property="image_url", type="string", maxLength=255, example="storage/CategoryImages/236095676.png")
     *          ),
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Category not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="ModelNotFoundException handled for API")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *         ),
     *      )
     * )
     */
    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,png'
        ]);

        $imageService = new ImageService;

        $url = $imageService->upload($request->file('image'), 'CategoryImages');

        $category = Category::findOrFail($id);
        $category->update(['image_url' => $url]);

        return response()->json(['image_url' => $url], 200);
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
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success deleting category",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Category is deleted successfully")
     *          ),
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Category not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="ModelNotFoundException handled for API")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *     )
     * ),
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->products()->delete();
        $category->delete();

        return response()->json(['message' => 'Category is deleted successfully'], 200);
    }
}
