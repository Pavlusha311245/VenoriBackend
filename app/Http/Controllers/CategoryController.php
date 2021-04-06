<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriesRequest;
use App\Models\Category;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Class CategoryController
 * @package App\Http\Controllers
 */
class CategoryController extends Controller
{
    /**
     * Display a category
     *
     * @return Application|Factory|View|JsonResponse
     */
    public function index()
    {
        return Category::paginate(5);
    }

    /**
     * Store a newly created category
     *
     * @param CategoriesRequest $request
     * @return Response
     */
    public function store(CategoriesRequest $request)
    {
        $category = Category::create($request->all());

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
     * Update category
     * @param CategoriesRequest $request
     * @param Category $category
     * @return JsonResponse
     */
    public function update(CategoriesRequest $request, Category $category)
    {
        $category->update($request->all());

        return response()->json($category, 200);
    }

    /**
     * Remove category
     *
     * @param $id
     * @return string
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->products()->delete();
        $category->delete();
        return response()->json(['message' => 'Category is deleted successfully'], 200);
    }
}
