<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

/**
 * Class ProductController for CRUD Products
 * @package App\Http\Controllers
 */
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Application|Factory|View|JsonResponse
     * @return
     */
    public function index()
    {
        Product::paginate(5);
    }

    /**
     * Import a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request){
        $data = $request->file('csv_file');

        $request->validate([
            'csv_file' => 'required|file',
        ]);

        $rows = array_map('str_getcsv', file($data));
        $header = array_shift($rows);

        foreach ($rows as $row){
            $csv[] = array_combine($header, $row);
        }

        /*foreach ($rows as $row) {
            $validator = Validator::make($row[],[
                'name' => 'required',
                'weight' => 'required',
                'price' => 'required',
                'category_id' => 'required'
            ]);

            if ($validator->fails()){
                return response(['error' => $validator->errors(), 'Validation Error']);
            }
        }*/

        foreach ($rows as $row){
            $products = [
                'name' => $row[0],
                'weight' => $row[1],
                'price' => $row[2],
                'category_id' => $row[3]
            ];

            Product::updateOrCreate(
                ['name' => $products['name']],
                [
                    'name' => $products['name'],
                    'weight' => $products['weight'],
                    'price' => $products['price'],
                    'category_id' => $products['category_id']
                ],
            );
        }
    }

    /**
     * Creates a resource in the storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $request->validate([
                'name' => 'required',
                'weight' => 'required',
                'price' => 'required',
                'category_id' => 'required'
        ]);

        $product = Product::create($request->all());
        return response($product, 201);
    }

    /**
     * Display a listing of the resource.
     * @return Application|Factory|View|JsonResponse
     * @return
     */
    public function get_product($name){
        return response()->json(Product::findOrFail($name), 200);
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
     * @return Application|ResponseFactory|JsonResponse|RedirectResponse|Response
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'weight' => 'required',
            'price' => 'required',
            'category_id' => 'required'
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
