<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Controller for create, remove, update and show schedules
 *
 * @package App\Http\Controllers
 */
class ScheduleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/schedules",
     *     summary="All schedules",
     *     description="Getting a list of all schedules",
     *     operationId="schedulesIndex",
     *     tags={"schedules"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Successfully received a list of scheduled places",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      ref="#/components/schemas/Schedule"
     *                  ),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *     ),
     * )
     */
    public function index()
    {
        return Schedule::paginate(7);
    }

    /**
     * @OA\Post(
     *     path="/api/schedules",
     *     summary="Adds 1 day to the schedule for a place",
     *     description="Adds 1 day to the schedule for a place",
     *     operationId="scheduleStore",
     *     tags={"schedules"},
     *     security={ {"bearer": {} }},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass data to add a new schedule",
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/Schedule"
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success storing a new schedule",
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/Schedule"
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *         ),
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
     *                      @OA\Items(
     *                          type="string",
     *                          example="The name field is required.",
     *                      )
     *                  )
     *              )
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'place_id' => 'required|numeric|min:1',
        ]);

        $schedule = Schedule::create($request->all());

        return response()->json($schedule, 201);
    }

    /**
     * Method show schedule by id
     * @param $id
     * @return Response
     */
    public function show($id)
    {
        return Schedule::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/schedules/{id}",
     *     summary="Update schedule",
     *     description="Updating schedule information",
     *     operationId="schedulesUpdate",
     *     tags={"schedules"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of day of the schedule",
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
     *          required=true,
     *          description="Pass data to update user information",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      ref="#/components/schemas/Schedule"
     *                  ),
     *              ),
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success updating category information",
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/Category"
     *          ),
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Review not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="ModelNotFoundException handled for API")
     *          )
     *       ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *         ),
     * )
     */
    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'place_id' => 'numeric|min:1',
        ]);

        $schedule->update($request->all());
        $schedule->save();

        return response()->json($schedule, 200);
    }
}
