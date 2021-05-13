<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Review;
use App\Services\Rating\PlaceRatingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

/**
 * Controller for adding, removing, viewing and updating reviews
 *
 * @package App\Http\Controllers
 */
class ReviewController extends Controller
{
    protected $placeRatingService;

    public function __construct(PlaceRatingService $placeRatingService)
    {
        $this->placeRatingService = $placeRatingService;
    }

    /**
     * @OA\Get(
     *     path="/api/reviews",
     *     summary="Review info",
     *     description="Getting a list of all reviews",
     *     operationId="reviewsIndex",
     *     tags={"reviews"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success getting a list of all reviews",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Review")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     )
     * )
     */
    public function index()
    {
        return Review::paginate(Config::get('constants.pagination.count'));
    }

    /**
     * @OA\Post(
     *     path="/api/reviews",
     *     summary="Add review",
     *     description="Adding a new review",
     *     operationId="reviewStore",
     *     tags={"reviews"},
     *     security={ {"bearer": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass data to add a new review",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", readOnly=true, example=1),
     *              @OA\Property(property="title", type="string", description="Review title", example="Gread place!!!"),
     *              @OA\Property(property="rating", type="number", description="User rating", example=5),
     *              @OA\Property(property="description", type="string", description="The main text of the review", example="Very cool restaurant, I liked everything"),
     *              @OA\Property(property="like", type="integer", description="The number of likes under the review from other users", example=1, readOnly=true),
     *              @OA\Property(property="place_id", type="integer", description="Id of the place for which the review is left", example=1),
     *              @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
     *              @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true)
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success storing a new review",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/Review")
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Review already exists",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Review already exists")
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
        $validateReviewData = $request->validate([
            'title' => 'required|string',
            'rating' => 'required|numeric|min:1|max:5',
            'description' => 'required|string',
            'place_id' => 'required'
        ]);

        Place::findOrFail($request->get('place_id'));

        if (Review::where('place_id', $request->get('place_id'))
                ->where('user_id', auth()->user()->id)->first() !== null)
            return response()->json(['message' => 'Review already exists'], 400);

        $validateReviewData['user_id'] = auth()->user()->id;

        $review = Review::create($validateReviewData);

        $this->placeRatingService->updatePlaceRatingAndReviewsCount($review);

        return response()->json($review, 201);
    }

    /**
     * Display the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $review = Review::findOrFail($id);

        if ($review->user_id != auth()->user()->id)
            return response()->json(['message' => 'Access denied']);

        return $review;
    }

    /**
     * @OA\Put(
     *     path="/api/reviews/{id}",
     *     summary="Update review",
     *     description="Updating review information",
     *     operationId="reviewUpdate",
     *     tags={"reviews"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of review",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *          required=false,
     *          description="Pass data to update review information",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/Review")
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success updating review information",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/Review")
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
     *          description="Review not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No review found")
     *          )
     *     )
     * )
     */
    public function update(Request $request, Review $review)
    {
        $validateReviewData = $request->validate([
            'title' => 'string',
            'rating' => 'numeric|min:1|max:5',
            'description' => 'string',
        ]);

        if ($review->user_id !== auth()->user()->id)
            return response()->json(['message' => 'Access denied'], 400);

        $review->update($validateReviewData);

        $this->placeRatingService->updatePlaceRatingAndReviewsCount($review);

        return response()->json($review);
    }

    /**
     * @OA\Delete(
     *     path="/api/reviews/{id}",
     *     summary="Removes review",
     *     description="Removes review",
     *     operationId="reviewsDelete",
     *     tags={"reviews"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of review",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success deleting review",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Review is deleted successfully")
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
     *          description="Review not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No review found")
     *          )
     *     )
     * )
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        if ($review->user_id !== auth()->user()->id)
            return response()->json(['message' => 'Access denied'], 400);

        $review->comments()->delete();
        $review->delete();

        $this->placeRatingService->updatePlaceRatingAndReviewsCount($review);

        return response()->json(['message' => 'Review is successfully deleted']);
    }

    /**
     * @OA\Get(
     *     path="/api/reviews/{id}/comments",
     *     summary="Get review comments",
     *     description="Getting a review comments",
     *     operationId="commentsReview",
     *     tags={"comments"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of review",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success getting a review comments",
     *          @OA\JsonContent(
     *              @OA\Items(ref="#/components/schemas/Comment")
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
     *          description="Review not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No review found")
     *          )
     *     )
     * )
     */
    public function getComments($id)
    {
        return Review::findOrFail($id)->comments()->paginate(Config::get('constants.pagination.count'));
    }
}
