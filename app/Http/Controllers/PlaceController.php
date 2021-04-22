<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Place;
use App\Models\Product;
use App\Models\ProductsOfPlace;
use App\Models\Review;
use App\Services\ImageService;
use App\Services\RadiusAroundLocationService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Controller for adding, deleting, updating and viewing catering establishments
 * @package App\Http\Controllers
 */
class PlaceController extends Controller
{
    /**
     * The method returns a list of all establishments
     * @return mixed
     */
    public function index(Request $request, RadiusAroundLocationService $radiusAroundLocationService)
    {
        if ($request->has('distance')) {
            $dist = $request->get('distance');
            $coordiantes = $radiusAroundLocationService->coordinates(auth()->user()->address_lat, auth()->user()->address_lon, $dist);
            return Place::whereBetween('address_lon', [$coordiantes['lon_start'], $coordiantes['lon_end']])
                ->whereBetween('address_lat', [$coordiantes['lat_start'], $coordiantes['lat_end']])
                ->get();
        }

        if ($request->has('name'))
            return Place::where('name', 'LIKE', "%" . $request->name . "%")->get();

        return Place::paginate(5);
    }

    /**
     * The method adds a new establishment
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:places',
            'type' => 'required|max:255',
            'address_full' => 'required|string',
            'address_lat' => 'required|numeric',
            'address_lon' => 'required|numeric',
            'phone' => 'required|max:15',
            'capacity' => 'required|integer',
            'table_price' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpg,png'
        ]);

        $imageService = new ImageService;
        $url = $imageService->upload($request->file('image'), 'PlacesImages');

        $data = $request->all();
        $data['image_url'] = $url;

        $place = Place::create($data);

        return response()->json($place, 201);
    }

    /**
     * The method returns information about 1 institution
     * @param int $id
     * @return Application|ResponseFactory|Response
     */
    public function show($id)
    {
        return Place::findOrFail($id);
    }

    /**
     * The method returns menu for place
     * @param int $id
     * @return JsonResponse
     */
    public function menu($id)
    {
        $place = Place::findOrFail($id);
        $products = ProductsOfPlace::where('place_id', $place->id)->get();

        foreach ($products as $product) {
            $menuItem = Product::where('id', $product->product_id)->first();
            $category = Category::where('id', $menuItem->category_id)->first();

            $menu[$category->name][] = $menuItem;
        }

        return response()->json($menu);
    }

    /**
     * The method updates the data of the establishment
     * @param Request $request
     * @param Place $place
     * @return JsonResponse
     */
    public function update(Request $request, Place $place)
    {
        $request->validate([
            'name' => 'max:255|unique:places',
            'type' => 'max:255',
            'address_full' => 'string',
            'address_lat' => 'float',
            'address_lon' => 'float',
            'phone' => 'max:15',
            'capacity' => 'integer',
            'table_price' => 'string',
            'description' => 'string',
            'image_url' => 'string'
        ]);

        $place->update($request->all());

        return response()->json($place, 200);
    }

    /**
     * The method removes establishments by id
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $place = Place::findOrFail($id);
        $place->schedules()->delete();
        $place->delete();

        return response()->json(['message' => 'Place is deleted successfully'], 200);
    }

    /**
     * @param $id
     * @return int
     */
    public function reviewsCount($id)
    {
        return count(Review::where('place_id', $id)->get());
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,png'
        ]);

        $imageService = new ImageService;

        $url = $imageService->upload($request->file('image'), 'PlacesImages');

        $place = Place::findOrFail($id);
        $place->update(['image_url' => $url]);

        return response()->json(['image_url' => $url], 200);
    }
}
