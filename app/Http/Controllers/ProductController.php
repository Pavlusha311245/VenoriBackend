<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

/**
 * Class ProductController for CRUD Products
 *
 * @package App\Http\Controllers
 */
class ProductController extends Controller
{
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
    public function index()
    {
        return Product::paginate(5);
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function create(Request $request)
    {
        $validData = $request->validate([
            'name' => 'required|min:2',
            'weight' => 'required|min:1',
            'price' => 'required|min:1',
            'image_url' => 'required',
            'category_id' => 'required|min:1'
        ]);

        $product = Product::create($validData);

        if ($product) {
            return redirect('/admin/products')->with('success', 'Create successful');
        }

        return redirect('/create')->withErrors('formError', 'Create failed');
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function edit(Request $request, $id)
    {
        $request->validate([
            'name' => 'min:2',
            'weight' => 'min:1',
            'price' => 'min:1',
            'image_url' => 'min:2',
            'category_id',
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());
        $product->save();

        return redirect('/admin/products/'.$id)->with(['success', 'Product was updated']);
    }

    /**
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function remove($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect('/admin/products/')->with(['success', 'Products was deleted']);
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
    public function import(Request $request){
        $data = $request->file('products');

        $request->validate([
            'products' => 'required|file|mimes:csv,txt',
        ]);


        $rows = array_map('str_getcsv', file($data));
        $header = array_shift($rows);

        foreach ($rows as $row){
            $products_file[] = array_combine($header, $row);
        }

        foreach ($rows as $row){
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
        $request->validate([
            'name' => 'required',
            'weight' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            'image_url' => 'required'
        ]);

        $product = Product::create($request->all());
        return response($product, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{name}",
     *     summary="Product info",
     *     description="Getting product info",
     *     operationId="productInfoGetProduct",
     *     tags={"products"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *         response=201,
     *         description="Success storing a new product",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="Milk"),
     *              @OA\Property(property="weight", type="string", example="200ml"),
     *              @OA\Property(property="price", type="number", example="user1@mail.com"),
     *              @OA\Property(property="category_id", type="integer", example=1),
     *              @OA\Property(property="image_url", type="string", example="app/public/ProductImages/248445071.png"),
     *              @OA\Property(property="created_at", type="string", format="date-time", example="2019-02-25 12:59:20"),
     *              @OA\Property(property="updated_at", type="string", format="date-time", example="2019-02-25 12:59:20"),
     *              @OA\Property(property="id", type="integer", example=3)
     *        )
     *    ),
     *    @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthenticated.")
     *          )
     *     )
     * )
     */
    public function getProduct($name)
    {
        return response()->json(Product::findOrFail($name));
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
     *          response=400,
     *          description="Product not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="ModelNotFoundException handled for API")
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
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product is deleted successfully']);
    }
}
