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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

/**
 * Controller for adding, deleting, updating and viewing catering establishments
 * @package App\Http\Controllers
 */
class PlaceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/places",
     *     summary="Places info",
     *     description="Getting a list of all establishments",
     *     operationId="placesIndex",
     *     tags={"places"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success getting a list of all establishment",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Place")
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
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function create(Request $request)
    {
        $validData = $request->validate([
            'name' => 'required|max:255',
            'type' => 'required|max:255',
            'address_full' => 'required|string',
            'address_lat' => 'required|numeric',
            'address_lon' => 'required|numeric',
            'phone' => 'required|max:15',
            'capacity' => 'required|integer',
            'table_price' => 'required|string',
            'description' => 'required|string',
            'image_url' => 'required|string'
        ]);

        $product = Place::create($validData);

        if ($product) {
            return redirect('/admin/places')->with('message', 'Create successful');
        }

        return redirect('/create')->withErrors('message', 'Create failed');
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function edit(Request $request, $id)
    {
        $request->validate([
            'name' => 'max:255',
            'type' => 'max:255',
            'address_full' => 'string',
            'address_lat' => 'required|numeric',
            'address_lon' => 'required|numeric',
            'phone' => 'max:15',
            'capacity' => 'integer',
            'table_price' => 'string',
            'description' => 'string',
            'image_url' => 'string'
        ]);

        $product = Place::findOrFail($id);
        $product->update($request->all());
        $product->save();

        return redirect('/admin/places/'.$id)->with('message', 'Place was updated');
    }

    /**
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function remove($id)
    {
        $product = Place::findOrFail($id);
        $product->delete();

        return redirect('/admin/places/')->with('message', 'Places was deleted');
    }

    /**
     * @OA\Post(
     *     path="/api/places",
     *     summary="Add establishment",
     *     description="Adding a new establishments",
     *     operationId="placesStore",
     *     tags={"places"},
     *     security={ {"bearer": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass data to add a new establishment",
     *          @OA\JsonContent(
     *              required={"name","type","address_full","address_lat", "address_lon", "phone", "capacity", "table_price", "description", "image_url"},
     *              @OA\Property(property="name", type="string", example="KFC"),
     *              @OA\Property(property="type", type="string", example="Fast food"),
     *              @OA\Property(property="address_full", type="string", example="adress.ul.address"),
     *              @OA\Property(property="address_lat", type="number", example=123),
     *              @OA\Property(property="address_lon", type="number", example=34),
     *              @OA\Property(property="phone", type="string", example="+375295637384"),
     *              @OA\Property(property="capacity", type="number", example=10),
     *              @OA\Property(property="table_price", type="string", example="12"),
     *              @OA\Property(property="description", type="string", example="Description of the KFC"),
     *              @OA\Property(property="image_url", type="string", example="app/public/PlaceImages/KFC.png")
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success storing a new establishment",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string", example="KFC"),
     *              @OA\Property(property="type", type="string", example="Fast food"),
     *              @OA\Property(property="address_full", type="string", example="adress.ul.address"),
     *              @OA\Property(property="address_lat", type="number", example=123),
     *              @OA\Property(property="address_lon", type="number", example=34),
     *              @OA\Property(property="phone", type="string", example="+375295637384"),
     *              @OA\Property(property="capacity", type="number", example=10),
     *              @OA\Property(property="table_price", type="string", example="12"),
     *              @OA\Property(property="description", type="string", example="Description of the KFC"),
     *              @OA\Property(property="image_url", type="string", example="app/public/PlaceImages/KFC.png"),
     *              @OA\Property(property="created_at", type="string", format="date-time", example="2021-04-21 18:22:20"),
     *              @OA\Property(property="updated_at", type="string", format="date-time", example="2021-04-21 18:2:20"),
     *              @OA\Property(property="id", type="integer", example=1)
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
     * @OA\Get(
     *     path="/api/places/{id}",
     *     summary="Places info",
     *     description="Getting information about 1 institution",
     *     operationId="placesShow",
     *     tags={"places"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success getting information about 1 institution",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Place")
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
        return Place::findOrFail($id);
    }

    /**
     * @OA\Get(
     *     path="/api/places/{id}/menu",
     *     summary="Places menu",
     *     description="Getting information about 1 institution",
     *     operationId="placesMenu",
     *     tags={"places"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success getting information about 1 institution",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="Coffee",
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
     * @OA\Put(
     *     path="/api/places/{id}",
     *     summary="Update establishment",
     *     description="Updating the data of the establishment",
     *     operationId="placesUpdate",
     *     tags={"places"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of place",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass data to update establishment information",
     *          @OA\JsonContent(
     *              required={"name","type","address_full","address_lat", "address_lon", "phone", "capacity", "table_price", "description", "image_url"},
     *              @OA\Property(property="name", type="string", example="Burger King"),
     *              @OA\Property(property="type", type="string", example="Fast food"),
     *              @OA\Property(property="address_full", type="string", example="adress.ul.address"),
     *              @OA\Property(property="address_lat", type="number", example=123),
     *              @OA\Property(property="address_lon", type="number", example=34),
     *              @OA\Property(property="phone", type="string", example="+375295637384"),
     *              @OA\Property(property="capacity", type="number", example=10),
     *              @OA\Property(property="table_price", type="string", example="12"),
     *              @OA\Property(property="description", type="string", example="Description of the Burger King"),
     *              @OA\Property(property="image_url", type="string", example="app/public/PlaceImages/BurgerKing.png")
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success updating establishment information",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="string", example=1),
     *              @OA\Property(property="name", type="string", example="Burger King"),
     *              @OA\Property(property="type", type="string", example="Fast food"),
     *              @OA\Property(property="address_full", type="string", example="adress.ul.address"),
     *              @OA\Property(property="address_lat", type="number", example=123),
     *              @OA\Property(property="address_lon", type="number", example=34),
     *              @OA\Property(property="phone", type="string", example="+375295637384"),
     *              @OA\Property(property="capacity", type="number", example=10),
     *              @OA\Property(property="table_price", type="string", example="12"),
     *              @OA\Property(property="description", type="string", example="Description of the Burger King"),
     *              @OA\Property(property="image_url", type="string", example="app/public/PlaceImages/BurgerKing.png"),
     *              @OA\Property(property="created_at", type="string", format="date-time", example="2021-04-21 18:22:20"),
     *              @OA\Property(property="updated_at", type="string", format="date-time", example="2021-04-21 18:2:20")
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
     * @OA\Delete(
     *     path="/api/places/{id}",
     *     summary="Delete place",
     *     description="Deleting establishment",
     *     operationId="placesDelete",
     *     tags={"places"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of place",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success deleting place",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Place is deleted successfully")
     *          )
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Place not found",
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
        $place = Place::findOrFail($id);
        $place->schedules()->delete();
        $place->delete();

        return response()->json(['message' => 'Place is deleted successfully'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/places/{id}/reviewsCount",
     *     summary="Returns the number of reviews for a place",
     *     description="Returns the number of reviews for a place",
     *     operationId="reviewsCount",
     *     tags={"places"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of category",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success storing a new user",
     *          @OA\JsonContent(
     *              @OA\Property(property="reviews_count", type="integer", maxLength=255, example=2)
     *          ),
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Category not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="ModelNotFoundException handled for API")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *         ),
     *      )
     * )
     */
    public function reviewsCount($id)
    {
        return response()->json(['reviews_count' => count(Review::where('place_id', $id)->get())]);
    }

    /**
     * @OA\Post(
     *     path="/api/places/{id}/uploadImage",
     *     summary="Updates the category picture",
     *     description="Updates the category picture",
     *     operationId="categoryUploadImage",
     *     tags={"categories"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of category",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *     ),
     *     @OA\RequestBody(
     *          required=false,
     *          description="Pass data to add a new category image",
     *          @OA\JsonContent(
     *              @OA\Property(property="image", type="file", maxLength=255, example="(file path)"),
     *     )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success storing a new user",
     *          @OA\JsonContent(
     *              @OA\Property(property="image_url", type="string", maxLength=255, example="storage/PlacesImages/236095676.png")
     *          ),
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Place not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="ModelNotFoundException handled for API")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *         ),
     *      )
     * )
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
