<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * CommentController for adding, deleting, updating and showing comments
 *
 * @package App\Http\Controllers
 */
class CommentController extends Controller
{
    /**
     * The method returns a list of all comments
     *
     * @return Response
     */
    public function index()
    {
        return Comment::paginate(5);
    }

    /**
     * The method adds a new comment
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:1',
            'description' => 'required|min:1',
            'review_id' => 'required',
        ]);

        $comment = Comment::create($request->all());

        return response($comment, 201);
    }

    /**
     * The method updates comment
     *
     * @param Request $request
     * @param Comment $comment
     * @return JsonResponse
     */
    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            'title' => 'min:1',
            'description' => 'min:1',
            'review_id' => 'required',
        ]);

        $comment->update($request->all());

        return response()->json($comment, 200);
    }

    /**
     * The method removes comment
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json(['message' => 'Comment is deleted successfully'], 200);
    }
}
