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
     * Reserve a Table
     *
     * @param ReservationTimeRequest $request
     * @param $place_id
     * @return JsonResponse
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
