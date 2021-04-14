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
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $imageService = new ImageService;

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
     * Update category
     * @param Request $request
     * @param Category $category
     * @return JsonResponse
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
     * The method upload the category of the user
     *
     * @param Request $request
     * @param int $id
     *
     */
    public function uploadImage(Request $request, $id)
    {
        $imageService = new ImageService;

        $url = $imageService->upload($request->file('image'), 'CategoryImages');

        $category = Category::findOrFail($id);

        $category->update(['image_url' => $url]);

        return response()->json($url, 200);
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
