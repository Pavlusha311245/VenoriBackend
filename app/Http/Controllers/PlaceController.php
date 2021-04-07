<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
     * @return Response
     */
    public function index()
    {
        return Place::paginate(5);
    }

    /**
     * The method adds a new establishment
     * @param Request $request
     * @return Response
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
            'description' => 'required|string',
            'schedule' => 'required|json',
        ]);

        $place = Place::create($request->all());

        return response($place, 201);
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
            'description' => 'string',
            'schedule' => 'required|json',
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
        $place->delete();

        return response()->json(['message' => 'Place is deleted successfully'], 200);
    }

    /**
     * The method finds establishments by the specified parameters
     * @param Request $request
     * @param Place $place
     * @return Application|ResponseFactory|Response
     */
    public function searchPlace(Request $request, Place $place)
    {
        $places = $place->all();

        if ($name = $request->get('name')) {
            $places = Place::where('name', 'LIKE', "%" . $name . "%")->get();
        }

        if ($type = $request->get('type')) {
            $places = Place::where('type', 'LIKE', "%" . $type . "%")->get();
        }

        if ($capacity = $request->get('capacity')) {
            $places = Place::where('capacity', 'LIKE', "%" . $capacity . "%")->get();
        }

        if ($rating = $request->get('rating')) {
            $places = Place::where('rating', '>=', $rating)->where('rating', '<', $rating + 1)->get();
        }

        return response($places, 200);
    }
}
