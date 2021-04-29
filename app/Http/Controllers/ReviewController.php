<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Review;
use App\Services\Rating\PlaceRatingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        return Review::paginate(5);
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
     *          @OA\JsonContent(type="object", ref="#/components/schemas/Review")
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success storing a new review",
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
            'title' => 'required|string',
            'rating' => 'required|numeric|min:1|max:5',
            'description' => 'required|string',
            'place_id' => 'required',
            'user_id' => 'required|unique:reviews'
        ]);

        $review = Review::create($request->all());

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
        return Review::findOrFail($id);
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
     *          response=400,
     *          description="Review not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="ModelNotFoundException handled for API")
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
    public function update(Request $request, Review $review)
    {
        $request->validate([
            'title' => 'string',
            'rating' => 'numeric|min:1|max:5',
            'description' => 'string',
        ]);

        $review->update($request->all());

        $this->placeRatingService->updatePlaceRatingAndReviewsCount($review);

        return response()->json($review, 200);
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
     *          response=400,
     *          description="Review not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="ModelNotFoundException handled for API")
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
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->comments()->delete();
        $review->delete();

        $this->placeRatingService->updatePlaceRatingAndReviewsCount($review);

        return response()->json(['message' => 'Review is successfully deleted'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/user/reviews",
     *     summary="Get user reviews",
     *     description="Getting auth user reviews",
     *     operationId="reviewsAuthUser",
     *     tags={"reviews"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success getting a auth user reviews",
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
     *     )
     * )
     */
    public function reviewsByUserId()
    {
        return Review::where('user_id', auth()->user()->id)->get();
    }

    /**
     * @OA\Get(
     *     path="/api/places/{id}/reviews",
     *     summary="Get place reviews",
     *     description="Getting place reviews",
     *     operationId="reviewsPlaceById",
     *     tags={"reviews"},
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
     *     )
     * )
     */

    public function reviewsByPlaceId($id)
    {
        return Review::where('place_id', $id)->get();
    }
}
