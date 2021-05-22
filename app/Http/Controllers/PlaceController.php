<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Favourite;
use App\Models\Place;
use App\Services\ImageService;
use App\Services\PaginateArrayService;
use App\Services\RadiusAroundLocationService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Throwable;

/**
 * Controller for adding, deleting, updating and viewing catering establishments
 * @package App\Http\Controllers
 */
class PlaceController extends Controller
{
    protected $imageService;
    protected $arrayPaginator;

    public function __construct(ImageService $imageService, PaginateArrayService $paginateArrayService)
    {
        $this->imageService = $imageService;
        $this->arrayPaginator = $paginateArrayService;
    }

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
     *              @OA\Property(property="id", type="integer", readOnly=true, example=1),
     *              @OA\Property(property="name", type="string", example="KFC"),
     *              @OA\Property(property="image_url", type="string", example="storage\PlaceImages\KFC.png"),
     *              @OA\Property(property="rating", type="number", example=4.23),
     *              @OA\Property(property="reviewsCount", type="number", example=5),
     *              @OA\Property(property="address_full", type="string", maxLength=255, example="Minsk"),
     *              @OA\Property(property="address_lat", type="number", example=53.913224),
     *              @OA\Property(property="address_lon", type="number", example=27.467663),
     *              @OA\Property(property="phone", type="string", maxLength=255, example="+375448675643"),
     *              @OA\Property(property="description", type="string", example="KFC (short for Kentucky Fried Chicken) is an American fast food restaurant."),
     *              @OA\Property(property="capacity", type="integer", example=45),
     *              @OA\Property(property="table_price", type="number", example=44.99),
     *              @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
     *              @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true),
     *              @OA\Property(property="favourite", type="number", example=true),
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
        $places = Place::query();

        if ($request->has('category'))
            $places = Category::findOrFail($request->get('category'))->places();

        if ($request->has('distance')) {
            $dist = $request->get('distance');

            $lat = auth()->user()->address_lat;
            $lon = auth()->user()->address_lon;
            $coordinates = $radiusAroundLocationService->coordinates($lat, $lon, $dist);

            $places->whereBetween('address_lon', [$coordinates['lon_start'], $coordinates['lon_end']])
                ->whereBetween('address_lat', [$coordinates['lat_start'], $coordinates['lat_end']])
                ->get();
        }

        if ($request->has('name'))
            $places->where('name', 'LIKE', "%" . $request->get('name') . "%")->get();

        $places = $places->get();

        foreach ($places as $place)
            $place['favourite'] = auth()->user()->favoutirePlaces()->find($place->id) !== null;

        return $this->arrayPaginator->paginate($places, Config::get('constants.pagination.count'));
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function create(Request $request)
    {
        $validatePlaceData = $request->validate([
            'name' => 'required|max:255',
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

        $url = $this->imageService->upload($request->file('image'), 'PlacesImages');

        $validatePlaceData['image_url'] = $url;

        $place = Place::create($validatePlaceData);

        if ($place)
            return redirect('/admin/places')->with('message', 'Create successful');

        return redirect('/create')->withErrors('message', 'Create failed');
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function edit(Request $request, $id)
    {
        $validatePlaceData = $request->validate([
            'name' => 'max:255',
            'type' => 'max:255',
            'address_full' => 'string',
            'address_lat' => 'required|numeric',
            'address_lon' => 'required|numeric',
            'phone' => 'max:15',
            'capacity' => 'integer',
            'table_price' => 'string',
            'description' => 'string',
            'image' => 'nullable|image|mimes:jpg,png'
        ]);

        $place = Place::findOrFail($id);

        if (isset($validatePlaceData['image'])) {
            $image_path = $place->image_url;

            if (File::exists($image_path)) {
                File::delete($image_path);
            }

            $url = $this->imageService->upload($request->file('image'), 'PlacesImages');

            $validatePlaceData['image_url'] = $url;
        }

        $place->update($validatePlaceData);
        $place->save();

        return redirect('/admin/places/' . $id)->with('message', 'Place was updated');
    }

    /**
     * @param $id
     * @return Application|RedirectResponse|Redirector
     */
    public function remove($id)
    {
        $place = Place::findOrFail($id);

        $this->imageService->delete($place->image_url);

        $place->products()->detach();
        $place->managers()->detach();
        $place->categories()->detach();
        $place->delete();

        return redirect('/admin/places/')->with('message', 'Places was deleted');
    }

    /**
     * @param Request $request
     * @param $id
     * @return int
     */
    public function addPlaceToManagement($id)
    {
        $managedPlaces = auth()->user()->managedPlaces;

        auth()->user()->managedPlaces()->attach($id);
    }

    /**
     * @param Place $id
     * @throws Throwable
     */
    public function removePlaceFromManagement(Place $id)
    {
        $managedPlaces = auth()->user()->managedPlaces;

        if ($managedPlaces->find($id) === null)
            return redirect()->withErrors('message', 'Such a place does not exist in the managed');

        auth()->user()->managedPlaces()->detach($id);
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
        $validatePlaceData = $request->validate([
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

        $url = $this->imageService->upload($request->file('image'), 'PlacesImages');

        $validatePlaceData['image_url'] = $url;

        $place = Place::create($validatePlaceData);

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
     *              @OA\Property(property="id", type="integer", readOnly=true, example=1),
     *              @OA\Property(property="name", type="string", example="KFC"),
     *              @OA\Property(property="image_url", type="string", example="storage\PlaceImages\KFC.png"),
     *              @OA\Property(property="rating", type="number", example=4.23),
     *              @OA\Property(property="reviewsCount", type="number", example=5),
     *              @OA\Property(property="address_full", type="string", maxLength=255, example="Minsk"),
     *              @OA\Property(property="address_lat", type="number", example=53.913224),
     *              @OA\Property(property="address_lon", type="number", example=27.467663),
     *              @OA\Property(property="phone", type="string", maxLength=255, example="+375448675643"),
     *              @OA\Property(property="description", type="string", example="KFC (short for Kentucky Fried Chicken) is an American fast food restaurant."),
     *              @OA\Property(property="capacity", type="integer", example=45),
     *              @OA\Property(property="table_price", type="number", example=44.99),
     *              @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
     *              @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true),
     *              @OA\Property(property="favourite", type="number", example=true),
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
     *          response=404,
     *          description="Place not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No place found")
     *          )
     *     )
     * )
     */
    public function show($id)
    {
        $place = Place::findOrFail($id);
        $place['favourite'] = Favourite::where('place_id', $id)->where('user_id', auth()->user()->id)->first() !== null;

        return $place;
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
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Place not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No place found")
     *          )
     *     )
     * )
     */
    public function menu($id)
    {
        $place = Place::findOrFail($id);

        $menu = [];

        foreach ($place->products as $product)
            $menu[$product->category->name][] = $product;

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
     *          response=404,
     *          description="Place not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No place found")
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

        return response()->json($place);
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
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Place not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No place found")
     *          )
     *     )
     * )
     */
    public function destroy($id)
    {
        $place = Place::findOrFail($id);
        $place->products()->detach();
        $place->managers()->detach();
        $place->categories()->detach();
        $place->delete();

        return response()->json(['message' => 'Place is deleted successfully']);
    }

    /**
     * @OA\Post(
     *     path="/api/places/{id}/uploadImage",
     *     summary="Updates the place picture",
     *     description="Updates the place picture",
     *     operationId="placeUploadImage",
     *     tags={"places"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of place",
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
     *          description="Pass data to add a new place image",
     *          @OA\JsonContent(
     *              @OA\Property(property="image", type="file", maxLength=255, example="(file path)"),
     *     )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success updated place image",
     *          @OA\JsonContent(
     *              @OA\Property(property="image_url", type="string", maxLength=255, example="storage/PlacesImages/236095676.png")
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *       ),
     *     @OA\Response(
     *          response=404,
     *          description="Place not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No place found")
     *          )
     *       )
     *    )
     * )
     */
    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,png'
        ]);

        $url = $this->imageService->upload($request->file('image'), 'PlacesImages');

        $place = Place::findOrFail($id);
        $place->update(['image_url' => $url]);

        return response()->json(['image_url' => $url]);
    }

    /**
     * @OA\Get(
     *     path="/api/places/{id}/reviews",
     *     summary="Get place reviews",
     *     description="Getting place reviews",
     *     operationId="reviewsPlaceById",
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
     *
     *     @OA\Response(
     *          response=200,
     *          description="Success getting a place reviews",
     *          @OA\JsonContent(
     *              @OA\Items(ref="#/components/schemas/Review")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Place not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No place found")
     *          )
     *     )
     * )
     */
    public function getReviews($id)
    {
        return Place::findOrFail($id)->reviews()->paginate(Config::get('constants.pagination.count'));
    }
}
