<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Product;
use App\Models\ProductsOfPlace;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

/**
 * Class ProductController for CRUD Products
 *
 * @package App\Http\Controllers
 */
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index()
    {
        return Product::paginate(5);
    }

    /**
     * Import a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function import(Request $request){
        $data = $request->file('products');

        $request->validate([
            'products' => 'required|file',
        ]);

        $rows = array_map('str_products', file($data));
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
    }

    /**
     * Creates a resource in the storage.
     *
     * @param Request $request
     * @return Response
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
     * Display a listing of the resource.
     * @param $name
     * @return Application|Factory|View|JsonResponse
     */
    public function getProduct($name)
    {
        return response()->json(Product::findOrFail($name), 200);
    }

    /**
     * The method returns menu for place
     * @param int $id
     * @return Response
     */
    public function showMenu($id)
    {
        $place = Place::findOrFail($id);

        $products = ProductsOfPlace::where('place_id', $place);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return Response|string
     */
    public function show($id)
    {
        return Product::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Product $product
     * @return Application|JsonResponse|RedirectResponse|Response
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'weight' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            'image_url' => 'required'
        ]);

        $product->update($request->all());

        return response()->json($product, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return string
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product is deleted successfully'], 200);
    }
}
