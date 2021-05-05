<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * CommentController for adding, deleting, updating and showing comments
 *
 * @package App\Http\Controllers
 */
class CommentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/comments",
     *     summary="Comments info",
     *     description="Getting a list of all comments",
     *     operationId="commentsIndex",
     *     tags={"comments"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success getting a list of all comments",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Comment")
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
        return Comment::paginate(Config::get('constants.pagination.count'));
    }

    /**
     * @OA\Post(
     *     path="/api/comments",
     *     summary="Add a new comment",
     *     description="Adding a new comment",
     *     operationId="commentsStore",
     *     tags={"comments"},
     *     security={ {"bearer": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass data to add a new comment",
     *          @OA\JsonContent(
     *              required={"title","description","review_id"},
     *              @OA\Property(property="title", type="string", example="OMG"),
     *              @OA\Property(property="description", type="string", example="It is an amazing place in my hometown."),
     *              @OA\Property(property="review_id", type="integer", example=1),
     *              @OA\Property(property="user_id", type="integer", example=1),
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success storing a new comment",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="title", type="string", example="OMG"),
     *              @OA\Property(property="description", type="string", example="It is an amazing place in my hometown."),
     *              @OA\Property(property="review_id", type="integer", example=1),
     *              @OA\Property(property="user_id", type="integer", example=1),
     *              @OA\Property(property="created_at", type="string", format="date-time", example="2019-02-25 12:59:20"),
     *              @OA\Property(property="updated_at", type="string", format="date-time", example="2019-02-25 12:59:20"),
     *              @OA\Property(property="id", type="integer", example=1)
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
     *                      property="review_id",
     *                      type="array",
     *                      @OA\Items(type="string", example="The review id field is required.")
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:1',
            'description' => 'required|min:1',
            'review_id' => 'required|integer',
            'user_id' => 'required|integer'
        ]);

        $comment = Comment::create($request->all());

        return response()->json($comment, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/comments/{id}",
     *     summary="Show user comments",
     *     description="Showing a new comment",
     *     operationId="commentsShow",
     *     tags={"comments"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of comment",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success showing comment",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="title", type="string", example="OMG"),
     *              @OA\Property(property="description", type="string", example="It is an amazing place in my hometown."),
     *              @OA\Property(property="review_id", type="integer", example=1),
     *              @OA\Property(property="user_id", type="integer", example=1),
     *              @OA\Property(property="created_at", type="string", format="date-time", example="2019-02-25 12:59:20"),
     *              @OA\Property(property="updated_at", type="string", format="date-time", example="2019-02-25 12:59:20")
     *          )
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Comment not found",
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
    public function show($id)
    {
        return Comment::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/comments/{id}",
     *     summary="Update comment",
     *     description="Updating comment information",
     *     operationId="commentsUpdate",
     *     tags={"comments"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of comment",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass data to update comment information",
     *          @OA\JsonContent(
     *              @OA\Property(property="title", type="string", example="OMG"),
     *              @OA\Property(property="description", type="string", example="It is an amazing place in my hometown."),
     *              @OA\Property(property="review_id", type="integer", example=1),
     *              @OA\Property(property="user_id", type="integer", example=1),
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success updating comment information",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="title", type="string", example="OMG"),
     *              @OA\Property(property="description", type="string", example="It is an amazing place in my hometown."),
     *              @OA\Property(property="review_id", type="integer", example=1),
     *              @OA\Property(property="user_id", type="integer", example=1),
     *              @OA\Property(property="created_at", type="string", format="date-time", example="2019-02-25 12:59:20"),
     *              @OA\Property(property="updated_at", type="string", format="date-time", example="2019-02-25 12:59:20")
     *          )
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Comment not found",
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
    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'title' => 'string|min:1',
            'description' => 'string|min:1',
            'review_id' => 'integer',
            'user_id' => 'integer'
        ]);

        $comment->update($request->all());

        return response()->json($comment);
    }

    /**
     * @OA\Delete(
     *     path="/api/comments/{id}",
     *     summary="Delete comment",
     *     description="Deleting comment",
     *     operationId="commentsDelete",
     *     tags={"comments"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of comment",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success deleting comment",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Comment is deleted successfully")
     *          )
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Comment not found",
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
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Comment is deleted successfully']);
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
     *     )
     * )
     */
    public function commentsByReviewId($id)
    {
        return Comment::where('review_id', $id)->get();
    }
}
