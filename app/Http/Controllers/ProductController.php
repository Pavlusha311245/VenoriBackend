<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * CRUD of Products Controller.
     */
    public function index(){
        $products = Product::all();
        return response(['prosucts' => ProductResource::collection($products), 'message' => 'Retrieved successfully'], 200);
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
        $csv = [];

        foreach ($rows as $row){
            $csv[] = array_combine($header, $row);
        }

        foreach ($rows as $row) {
            $validator = Validator::make($row[],[
                'name' => 'required',
                'weight' => 'required',
                'price' => 'required',
                'category_id' => 'required'
            ]);

            if ($validator->fails()){
                return response(['error' => $validator->errors(), 'Validation Error']);
            }
        }

        $this->store($rows);
    }

    /**
     * Creates a resource in the storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($rows){
        foreach ($rows as $row) {
            $product = Product::create([
                'name' => $row[0],
                'weight' => $row[1],
                'price' => $row[2],
                'category_id' => $row[3],
            ]);
        }

        return response(['product' => new ProductResource($product), 'message' => 'Created successfully'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response(['product' => new ProductResource($product), 'message' => 'Retrieved successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product){
        $product->update($request->all());
        return response(['product' => new ProductResource($product), 'message' => 'Update successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Product $product){
        $product->delete();
        return response(['message' => 'Product is deleted']);
    }


}
