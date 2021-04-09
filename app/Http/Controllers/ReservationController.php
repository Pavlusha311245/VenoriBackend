<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationTimeRequest;
use App\Models\Order;
use App\Models\Place;
use App\Models\Schedule;
use App\Services\ReservationService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\JsonResponse;

/**
 * Class ReservationController for Reservation logic
 *
 * @package App\Http\Controllers
 */
class ReservationController extends Controller
{
    protected $reservation_service;
    protected $price = 40;

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
     * Get array of Available Times
     *
     * @param ReservationTimeRequest $request
     * @param $place_id
     * @return mixed
     */
    public function availableTime(ReservationTimeRequest $request, $place_id)
    {
        $date = Carbon::parse($request->date);
        $dayOfTheWeek = $date->dayOfWeekIso;

        $work_start = Schedule::findOrFail($place_id)
            ->where('id', $dayOfTheWeek % 7)
            ->value('work_start');

        $work_end = Schedule::findOrFail($place_id)
            ->where('id', $dayOfTheWeek % 7)
            ->value('work_end');

        if ($work_start == null)
            return response()->json(['message' => 'It\'s Day off']);

        $timeOfTable = $request->time;
        if ($date->isToday() && $timeOfTable != null) {
            $work_start = 0;
        } else {
            $work_start = $timeOfTable;
        }

        $capacityOnPlace = Place::findOrFail($place_id)->capacity;
        if ($request->people > $capacityOnPlace)
            return response()->json(['message' => 'Bad People']);

        $times = $this->reservation_service
            ->getTimes($work_start, $work_end);
        $bad_times = $this->reservation_service
            ->getBadTimes($place_id, $request->people, $times, $capacityOnPlace, $request->date);

        return $this->reservation_service->getAvailableTimes($bad_times, $times);
    }

    /**
     * Reserve a Table
     *
     * @param ReservationTimeRequest $request
     * @param $place_id
     * @return JsonResponse
     */
    public function tableReserve(ReservationTimeRequest $request, $place_id)
    {
        Place::findOrFail($place_id);

        $staying_end = date('H:i', (strtotime($request->time) + ($request->staying * 3600)));

        $order = Order::create([
            'status' => 'In Progress',
            'price' => $this->price,
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
