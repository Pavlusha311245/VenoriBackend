<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $places = Place::paginate(5);

        return \response($places,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'name' => 'required|max:255|unique:places',
            'type' => 'required|max:255',
            'address_address' => 'string',
            'address_latitude' => 'float',
            'address_longitude' => 'float',
            'phone' => 'required|max:15',
            'work_start' => 'required|string',
            'work_end' => 'required|string',
            'capacity' => 'required|string',
            'description' => 'required|string'
        ]);

        $place = Place::create($request->all());

        return response($place, 201);
    }

    /**
     * @param int $id
     * @return Application|ResponseFactory|Response
     */
    public function show(int $id)
    {
        try {
            return Place::findOrFail($id);
        } catch (ModelNotFoundException $ex) {
            return response(['message' => 'Place not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Place $place
     * @return Response
     */
    public function update(Request $request, Place $place): Response
    {
        $request->validate([
            'name' => 'max:255|unique:places',
            'type' => 'max:255',
            'address_address' => 'string',
            'address_latitude' => 'float',
            'address_longitude' => 'float',
            'phone' => 'max:15',
            'work_start' => 'string',
            'work_end' => 'string',
            'capacity' => 'string',
            'description' => 'string'
        ]);
        $place->update($request->all());
        return response(['message' => 'Place is updated successfully'], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        try {
            $place = Place::findOrFail($id);
            $place->delete();
            return \response(['message' => 'Place is deleted successfully'], 200);
        } catch (ModelNotFoundException $ex) {
            return \response(['error' => 'Place not fount'], 404);
        }
    }
}
