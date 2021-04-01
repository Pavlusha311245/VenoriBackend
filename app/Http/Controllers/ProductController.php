<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Application|Factory|View|JsonResponse
     * @return
     */
    public function index()
    {
        $products = Product::paginate(5);

        return response()->json($products, 200);
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
        return response(['product' => new $product, 'message' => 'Created successfully'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return Response|string
     */
    public function show($id)
    {
        try {
            return Product::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Product Is Not Found'], 201);
        }
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

        return response()->json(['message' => 'Product Is Updated Successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return string
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return response()->json(['message' => 'Product is deleted successfully'], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Product Is Not Found'], 201);
        }
    }
}
