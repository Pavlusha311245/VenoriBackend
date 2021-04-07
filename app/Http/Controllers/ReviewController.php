<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Review;
use App\Services\Rating\PlaceRatingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Controller for adding, removing, viewing and updating reviews
 *
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
     * @param PlaceRatingService $placeRatingService
     * @return JsonResponse
     */
    public function store(Request $request, PlaceRatingService $placeRatingService)
    {
        $request->validate([
            'title' => 'required|string',
            'rating' => 'required|numeric|min:1|max:5',
            'description' => 'required|string',
            'place_id' => 'required',
            'user_id' => 'required|unique:reviews'
        ]);

        $review = Review::create($request->all());

        $placeRatingService->countPlaceRating($review);

        return response()->json($review, 201);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return Review::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param Review $review
     * @param PlaceRatingService $placeRatingService
     * @return JsonResponse
     */
    public function update(Request $request, Review $review, PlaceRatingService $placeRatingService)
    {
        $request->validate([
            'title' => 'string',
            'rating' => 'numeric|min:1|max:5',
            'description' => 'string',
        ]);

        $review->update($request->all());

        $placeRatingService->countPlaceRating($review);

        return response()->json($review, 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @param PlaceRatingService $placeRatingService
     * @return JsonResponse
     */
    public function destroy($id, PlaceRatingService $placeRatingService)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        $placeRatingService->countPlaceRating($review);

        return response()->json(['message' => 'Review is successfully deleted'], 200);
    }
}
