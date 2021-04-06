<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Controller for adding, removing, viewing and updating reviews
 * @package App\Http\Controllers
 */
class ReviewController extends Controller
{
    /**
     * Method for returning all reviews
     * @return Response
     */
    public function index()
    {
        return Review::paginate(5);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
           'title' => 'required|string',
           'rating' => 'required|numeric|min:1|max:5',
           'description' => 'required|string'
        ]);

        $review = Review::create($request->all());

        return response()->json($review,201);
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return Review::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, Review $review)
    {
        $request->validate([
            'title' => 'string',
            'rating' => 'numeric|min:1|max:5',
            'description' => 'string'
        ]);
        $review->update($request->all());

        return response()->json($review,200);
    }

    /**
     * Remove the specified resource from storage.l
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json(['message' => 'Review is succesfully deleted'],200);
    }
}
