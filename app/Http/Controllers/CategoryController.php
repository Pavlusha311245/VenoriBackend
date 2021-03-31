<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
 * Class CategoryController
 * @package App\Http\Controllers
 */
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|JsonResponse
     */
    public function index()
    {
        $categories = Category::paginate(5);

        //return view('categories.show', ['categories' => $categories]);
        return response()->json($categories, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories',
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

        $category = Category::create($request->all());

        return response($category, 201);
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
            return Category::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Category Is Not Found'], 201);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Category $category
     * @return Application|ResponseFactory|JsonResponse|RedirectResponse|Response
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|unique:categories',
        ]);

        $category->update($request->all());

        return response()->json(['message' => 'Category Is Updated Successfully'], 200);
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
            $category = Category::findOrFail($id);
            $category->products()->delete();
            $category->delete();
            return response()->json(['message' => 'Category is deleted successfully'], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Category Is Not Found'], 201);
        }
    }
}
