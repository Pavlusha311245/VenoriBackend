<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationTimeRequest;
use App\Models\Order;
use App\Models\Place;
use App\Models\Schedule;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

/**
 * Class ReservationController for Reservation logic
 *
 * @package App\Http\Controllers
 */
class ReservationController extends Controller
{
    protected $reservation_service;

    /**
     * ReservationService constructor.
     *
     * @param ReservationService $reservation_service
     */
    public function __construct(ReservationService $reservation_service)
    {
        $this->reservation_service = $reservation_service;
    }

    /**
     * @OA\Post (
     *     path="/api/places/{id}/reservation",
     *     summary="Returning an array of free time",
     *     description="Displays free time",
     *     operationId="availableTime",
     *     tags={"reservation"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of place",
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
     *          description="Enter data to find out free time",
     *          @OA\JsonContent(
     *              @OA\Property(property="date", type="date", example="2021-04-19"),
     *              @OA\Property(property="people", type="integer", example="1"),
     *              @OA\Property(property="staying", type="float", example="0.5"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="available time array",
     *          @OA\JsonContent(
     *          @OA\Items(
     *              @OA\Items(
     *                      type="string",
     *                      example="10:00 AM",
     *                  )
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  @OA\Property(
     *                      property="date",
     *                      type="array",
     *                      @OA\Items(
     *                          type="string",
     *                          example="The date must be a date after yesterday.",
     *                      )
     *                  )
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *          response=400,
     *          description="Place not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="ModelNotFoundException handled for API")
     *          )
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
    public function availableTime(ReservationTimeRequest $request, $place_id)
    {
        $date = Carbon::parse($request->date);
        $dayOfTheWeek = $date->dayOfWeekIso;

        $work_start = Schedule::findOrFail($place_id)->where('id', $dayOfTheWeek % 7)->value('work_start');
        $work_end = Schedule::findOrFail($place_id)->where('id', $dayOfTheWeek % 7)->value('work_end');
        if ($work_start == null)
            return response()->json(['message' => 'It\'s Day off']);

        $capacityOnPlace = Place::findOrFail($place_id)->capacity;
        if ($request->people > $capacityOnPlace)
            return response()->json(['message' => 'Bad value of people']);

        $times = $this->reservation_service->getTimes($work_start, $work_end);

        if ($date->isToday())
        {
            $current_time = date(('12') ? 'g:i A' : 'G:i', strtotime(Carbon::now()->toDateTimeString()));
            foreach ($times as $time)
                if ($current_time <= $time)
                {
                    $work_start = $time;
                    $times = $this->reservation_service->getTimes($work_start, $work_end);
                    break;
                }
        }

        if ($request->staying > (array_key_last($times) / 2))
                return response()->json(['message' => 'Bad value of staying']);

        $bad_times = $this->reservation_service
            ->getBadTimes($place_id, $request->people, $times, $capacityOnPlace, $request->date);

        return $this->reservation_service->getAvailableTimes($bad_times, $times);
    }

    /**
     * @OA\Post (
     *     path="/api/places/{id}/reserve",
     *     summary="Create and return the order",
     *     description="Creates a table reservation for the selected date, time, number of people",
     *     operationId="tableReserve",
     *     tags={"reservation"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of place",
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
     *          description="Enter data to find out free time",
     *          @OA\JsonContent(
     *              @OA\Property(property="date", type="date", example="2021-04-21"),
     *              @OA\Property(property="people", type="integer", example="10"),
     *              @OA\Property(property="staying", type="float", example="2"),
     *              @OA\Property(property="time", type="time", example="17:00"),
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
     *                      property="date",
     *                      type="array",
     *                      @OA\Items(
     *                          type="string",
     *                          example="The date must be a date after yesterday.",
     *                      )
     *                  )
     *              )
     *          )
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated."),
     *          )
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Place not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="ModelNotFoundException handled for API")
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="The order has been successfully created. Status: 'In progress' ",
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/Order"
     *          ),
     *      )
     * )
     * )
     */
    public function tableReserve(ReservationTimeRequest $request, $place_id)
    {
        $staying_end = date('H:i', (strtotime($request->time) + ($request->staying * 3600)));
        $tablePrice = Place::findOrFail($place_id)->value('table_price');

        $price = (int)($request->people * $tablePrice);
        $order = Order::create([
            'status' => 'In Progress',
            'price' => $price,
            'date' => $request['date'],
            'people' => $request['people'],
            'time' => $request['time'],
            'staying' => $request['staying'],
            'staying_end' => $staying_end,
            'user_id' => auth()->user()->id,
            'place_id' => $place_id,
        ]);

        return response()->json($order, 200);
    }
}
