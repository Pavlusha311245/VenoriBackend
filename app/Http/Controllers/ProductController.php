<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Class ProductController for CRUD Products
 *
 * @package App\Http\Controllers
 */
class ProductController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Products info",
     *     description="Getting a list of all products",
     *     operationId="productsIndex",
     *     tags={"products"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success getting a list of all products",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Product")
     *              )
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
    public function index(Request $request)
    {
        $products = Product::query();

        if ($request->has('name'))
            $products->where('name', 'LIKE', "%" . $request->get('name') . "%");

        return $products->paginate(Config::get('constants.pagination.count'));
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function create(Request $request, $place_id)
    {
        $validateProductData = $request->validate([
            'name' => 'required|min:2',
            'weight' => 'required|min:1',
            'price' => 'required|min:1',
            'image' => 'required|mimes:png,jpg',
            'category_id' => 'required|min:1'
        ]);

        $url = $this->imageService->upload($request->file('image'), 'ProductImages');

        $validateProductData['image_url'] = $url;

        $product = Product::create($validateProductData);

        $place = Place::findOrFail($place_id);
        $place->products()->attach($product->id);

        if ($product)
            return redirect('/admin/places')->with('message', 'Create successful');

        return redirect('/create')->withErrors('message', 'Create failed');
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function edit(Request $request, $id)
    {

        $validateProductData = $request->validate([
            'name' => 'min:2',
            'weight' => 'min:1',
            'price' => 'min:1',
            'image' => 'mimes:png,jpg',
            'category_id' => 'min:1'
        ]);

        $url = $this->imageService->upload($request->file('image'), 'ProductImages');

        $validateProductData['image_url'] = $url;

        $product = Product::findOrFail($id);

        $product->update($validateProductData);

        return redirect('/admin/products/' . $id)->with('message', 'Product was updated');
    }

    /**
     * @param $place_id
     * @param $product_id
     * @return Application|RedirectResponse|Redirector
     */
    public function remove($place_id, $product_id)
    {
        $place = Place::select(['id'])->findOrFail($place_id);
        $image = $place->products()->find($product_id)->image_url;
        $place->products()->detach($product_id);

        $product = Product::findOrFail($product_id);
        $product->delete();

        if (!DB::table('products_of_places')->where('image_url', $image)->exists())
            $this->imageService->delete($product->image_url);

        return redirect('/admin/places/')->with('message', 'Products was deleted');
    }

    /**
     * @OA\Post(
     *     path="/api/products/import",
     *     summary="Import product",
     *     description="Import are new products",
     *     operationId="productsImport",
     *     tags={"products"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="Csv file for products",
     *          in="path",
     *          name="file",
     *          required=true,
     *          @OA\Schema(type="file", format="file")
     *     ),
     *     @OA\Response(response=200, description="Success importing a new product"),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthenticated.")
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
     *      )
     * )
     */
    public function import(Request $request)
    {
        $data = $request->file('products');

        $request->validate([
            'products' => 'required|file|mimes:csv,txt',
        ]);

        $rows = array_map('str_getcsv', file($data));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $products_file[] = array_combine($header, $row);
        }

        foreach ($rows as $row) {
            $products = [
                'name' => $row[0],
                'weight' => $row[1],
                'price' => $row[2],
                'category_id' => $row[3],
                'image_url' => $row[4]
            ];

            Product::updateOrCreate(
                ['name' => $products['name']],
                [
                    'name' => $products['name'],
                    'weight' => $products['weight'],
                    'price' => $products['price'],
                    'category_id' => $products['category_id'],
                    'image_url' => $products['image_url']
                ]);
        }

        return response()->json(['message' => "Success importing a new product"]);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Add product",
     *     description="Adding a new product",
     *     operationId="productsStore",
     *     tags={"products"},
     *     security={ {"bearer": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass data to add a new product",
     *          @OA\JsonContent(
     *              required={"name","weight","price","category_id", "image_url"},
     *              @OA\Property(property="name", type="string", example="Milk"),
     *              @OA\Property(property="weight", type="string", example="200ml"),
     *              @OA\Property(property="price", type="number", example=10),
     *              @OA\Property(property="category_id", type="integer", example=1),
     *              @OA\Property(property="image_url", type="string", example="app/public/ProductImages/248445071.png")
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success storing a new product",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Milk"),
     *              @OA\Property(property="weight", type="string", example="200ml"),
     *              @OA\Property(property="price", type="number", example="user1@mail.com"),
     *              @OA\Property(property="category_id", type="integer", example=1),
     *              @OA\Property(property="image_url", type="string", example="app/public/ProductImages/248445071.png"),
     *              @OA\Property(property="created_at", type="string", format="date-time", example="2019-02-25 12:59:20"),
     *              @OA\Property(property="updated_at", type="string", format="date-time", example="2019-02-25 12:59:20"),
     *              @OA\Property(property="id", type="integer", example=3)
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthenticated.")
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
     *      )
     * )
     */
    public function store(Request $request)
    {
        $validateProductData = $request->validate([
            'name' => 'required',
            'weight' => 'required',
            'price' => 'required',
            'place_id' => 'required',
            'category_id' => 'required',
            'image' => 'mimes:png,jpg',
        ]);

        $place = Place::findOrFail($request->get('place_id'));

        $url = $this->imageService->upload($request->file('image'), 'ProductImages');

        $validateProductData['image_url'] = $url;

        $product = Product::create($validateProductData);

        $place->products()->attach($product->id);

        return response($product, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Product show",
     *     description="Showing product by id",
     *     operationId="productsShow",
     *     tags={"products"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success showing product by id",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Product")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Product not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No product found")
     *          )
     *     )
     * )
     */
    public function show($id)
    {
        return Product::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update product",
     *     description="Updating product information",
     *     operationId="productsUpdate",
     *     tags={"products"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of product",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass data to update product information",
     *          @OA\JsonContent(
     *              required={"name","weight","price","category_id", "image_url"},
     *              @OA\Property(property="name", type="string", example="Cheese"),
     *              @OA\Property(property="weight", type="string", example="200ml"),
     *              @OA\Property(property="price", type="number", example=10),
     *              @OA\Property(property="category_id", type="integer", example=2),
     *              @OA\Property(property="image_url", type="string", example="app/public/ProductImages/cheese.png")
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success updating user information",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="Cheese"),
     *              @OA\Property(property="weight", type="string", example="200ml"),
     *              @OA\Property(property="price", type="string", example="10"),
     *              @OA\Property(property="avatar", type="string", example="storage/ProductImages/cheese.png"),
     *              @OA\Property(property="created_at", type="string", format="date-time", example="2021-04-15T12:37:21.000000Z"),
     *              @OA\Property(property="updated_at", type="string", format="date-time", example="2021-04-15T13:07:18.000000Z")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Product not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No product found")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object")
     *          )
     *      )
     * )
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'string',
            'weight' => 'string',
            'price' => 'integer',
            'category_id' => 'integer',
            'image_url' => 'string'
        ]);

        $product->update($request->all());

        return response()->json($product);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete product",
     *     description="Deleting product",
     *     operationId="productsDelete",
     *     tags={"products"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of product",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success deleting product",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Product is deleted successfully")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Product not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No product found")
     *          )
     *     )
     * )
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->places()->detach();
        $product->delete();

        $this->imageService->delete($product->image_url);

        return response()->json(['message' => 'Product is deleted successfully']);
    }
}
