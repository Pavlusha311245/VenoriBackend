<?php

namespace App\Http\Controllers;

use App\Models\Place;
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
            return Place::where('name', 'LIKE', "%".$request->name."%")->get();

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
            'address_address' => 'string',
            'address_latitude' => 'float',
            'address_longitude' => 'float',
            'phone' => 'required|max:15',
            'capacity' => 'required|string',
            'table_price' => 'required|string',
            'description' => 'required|string',
            'image_url' => 'required|string'
        ]);

        $place = Place::create($request->all());

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
            'location' => 'string',
            'phone' => 'max:15',
            'capacity' => 'string',
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
}
