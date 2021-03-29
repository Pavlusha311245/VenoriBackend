<?php

namespace App\Http\Controllers;

use App\Exceptions\CategoryNotFoundException;
use App\Http\Services\CategoryService;
use App\Models\Category;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

/**
 * Class CategoryController
 * @package App\Http\Controllers
 */
class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|JsonResponse
     */
    public function index()
    {
        $categories = Category::paginate(5);

        return view('categories.show', ['categories' => $categories]);
        //return response()->json($categories, 200);
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
            $category = (new CategoryService())->findById($id);
            $category->load(['products']);
        } catch (CategoryNotFoundException $exception) {
            return view('categories.notfound', ['error' => $exception->getMessage()]);
        }
        return Category::all();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Category $category
     * @return Application|ResponseFactory|RedirectResponse|Response
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')
            ->with('success','Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return string
     */
    public function destroy($id)
    {
        /*
        $category = Category::find($id);
        $category->products()->detach([$id]);
        $category->delete();
        */
        if (is_array($id))
        {
            Category::destroy($id);
        }
        else
        {
            Category::findOrFail($id)->delete();
        }

        return redirect()->route('categories.index')
            ->with('success','Category deleted successfully');
    }
}
