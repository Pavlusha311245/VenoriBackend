<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

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
     *                  @OA\Items(type="object", ref="#/components/schemas/Schedule")
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
        return Schedule::paginate(Config::get('constants.pagination.scheduleCount'));
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
     *          @OA\JsonContent(type="object", ref="#/components/schemas/Schedule")
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success storing a new schedule",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/Schedule")
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
            'place_id' => 'required|numeric|min:1',
        ]);

        $schedule = Schedule::create([
            'place_id' => $request->get('place_id'),
            'work_start' => Carbon::parse($request->get('work_start'))->format('g:i A'),
            'work_end' => Carbon::parse($request->get('work_end'))->format('g:i A'),
        ]);

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
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass data to update user information",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Schedule")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=201,
     *          description="Success updating category information",
     *          @OA\JsonContent(type="object", ref="#/components/schemas/Category")
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
     *          description="Schedule not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No schedule found")
     *          )
     *     )
     * )
     */
    public function update(Request $request, Schedule $schedule)
    {
        $schedule->update([
            'work_start' => Carbon::parse($request->get('work_start'))->format('g:i A'),
            'work_end' => Carbon::parse($request->get('work_end'))->format('g:i A'),
        ]);
        $schedule->save();

        return response()->json($schedule);
    }

    /**
     * @OA\Get(
     *     path="/api/places/{id}/schedule",
     *     summary="Get place schedule",
     *     description="Getting place schedule",
     *     operationId="schedulesPlaceById",
     *     tags={"schedules"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of place",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success getting a place schedule",
     *          @OA\JsonContent(
     *              @OA\Items(
     *                  oneOf={
     *                      @OA\Property(
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", readOnly=true, example=1),
     *                          @OA\Property(property="place_id", type="integer", description="Id of the place to which the schedule belongs", example=1),
     *                          @OA\Property(property="work_start", type="string", format="time", description="Start time", example="8:00"),
     *                          @OA\Property(property="work_end", type="string", format="time", description="End time", example="19:00"),
     *                          @OA\Property(property="lunch_start", type="string", format="time", description="lunch start timee", example="12:00"),
     *                          @OA\Property(property="lunch_end", type="string", format="time", description="lunch end time", example="13:00"),
     *                          @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
     *                          @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true)
     *                      ),
     *                      @OA\Property(
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", readOnly=true, example=2),
     *                          @OA\Property(property="place_id", type="integer", description="Id of the place to which the schedule belongs", example=1),
     *                          @OA\Property(property="work_start", type="string", format="time", description="Start time", example="8:00"),
     *                          @OA\Property(property="work_end", type="string", format="time", description="End time", example="19:00"),
     *                          @OA\Property(property="lunch_start", type="string", format="time", description="lunch start timee", example="12:00"),
     *                          @OA\Property(property="lunch_end", type="string", format="time", description="lunch end time", example="13:00"),
     *                          @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
     *                          @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true)
     *                      ),
     *                      @OA\Property(
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", readOnly=true, example=3),
     *                          @OA\Property(property="place_id", type="integer", description="Id of the place to which the schedule belongs", example=1),
     *                          @OA\Property(property="work_start", type="string", format="time", description="Start time", example="8:00"),
     *                          @OA\Property(property="work_end", type="string", format="time", description="End time", example="19:00"),
     *                          @OA\Property(property="lunch_start", type="string", format="time", description="lunch start timee", example="12:00"),
     *                          @OA\Property(property="lunch_end", type="string", format="time", description="lunch end time", example="13:00"),
     *                          @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
     *                          @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true)
     *                      ),
     *                      @OA\Property(
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", readOnly=true, example=4),
     *                          @OA\Property(property="place_id", type="integer", description="Id of the place to which the schedule belongs", example=1),
     *                          @OA\Property(property="work_start", type="string", format="time", description="Start time", example="8:00"),
     *                          @OA\Property(property="work_end", type="string", format="time", description="End time", example="19:00"),
     *                          @OA\Property(property="lunch_start", type="string", format="time", description="lunch start timee", example="12:00"),
     *                          @OA\Property(property="lunch_end", type="string", format="time", description="lunch end time", example="13:00"),
     *                          @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
     *                          @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true)
     *                      ),
     *                      @OA\Property(
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", readOnly=true, example=5),
     *                          @OA\Property(property="place_id", type="integer", description="Id of the place to which the schedule belongs", example=1),
     *                          @OA\Property(property="work_start", type="string", format="time", description="Start time", example="8:00"),
     *                          @OA\Property(property="work_end", type="string", format="time", description="End time", example="19:00"),
     *                          @OA\Property(property="lunch_start", type="string", format="time", description="lunch start timee", example="12:00"),
     *                          @OA\Property(property="lunch_end", type="string", format="time", description="lunch end time", example="13:00"),
     *                          @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
     *                          @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true)
     *                      ),
     *                      @OA\Property(
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", readOnly=true, example=6),
     *                          @OA\Property(property="place_id", type="integer", description="Id of the place to which the schedule belongs", example=1),
     *                          @OA\Property(property="work_start", type="string", format="time", description="Start time", example="8:00"),
     *                          @OA\Property(property="work_end", type="string", format="time", description="End time", example="19:00"),
     *                          @OA\Property(property="lunch_start", type="string", format="time", description="lunch start timee", example="12:00"),
     *                          @OA\Property(property="lunch_end", type="string", format="time", description="lunch end time", example="13:00"),
     *                          @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
     *                          @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true)
     *                      ),
     *                      @OA\Property(
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", readOnly=true, example=7),
     *                          @OA\Property(property="place_id", type="integer", description="Id of the place to which the schedule belongs", example=1),
     *                          @OA\Property(property="work_start", type="string", format="time", description="Start time", example="8:00"),
     *                          @OA\Property(property="work_end", type="string", format="time", description="End time", example="19:00"),
     *                          @OA\Property(property="lunch_start", type="string", format="time", description="lunch start timee", example="12:00"),
     *                          @OA\Property(property="lunch_end", type="string", format="time", description="lunch end time", example="13:00"),
     *                          @OA\Property(property="created_at", type="string", format="date-time", description="Initial creation timestamp", readOnly=true),
     *                          @OA\Property(property="updated_at", type="string", format="date-time", description="Last update timestamp", readOnly=true)
     *                      )
     *                  }
     *              )
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
     *          description="Place not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No place found")
     *          )
     *     )
     * )
     */
    public function scheduleByPlaceId($id)
    {
        return Schedule::where('place_id', $id)->limit(7)->get();
    }
}
