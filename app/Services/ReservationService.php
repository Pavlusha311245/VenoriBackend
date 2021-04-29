<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Http\JsonResponse;

/**
 * Class ReservationService for Reservation logic
 * @package App\Http\Services
 */
class ReservationService
{
    /**
     * Get array of Times
     *
     * @param $work_start
     * @param $work_end
     * @return array
     */
    public function getTimes($work_start, $work_end)
    {
        $startTime = Carbon::parse($work_start);
        $endTime = Carbon::parse($work_end);

        $rangeTimes = CarbonInterval::minutes(30)->toPeriod($startTime, $endTime->addMinutes(-30));

        foreach ($rangeTimes as $time) {
            $times[] = $time->format('g:i A');
        }

        return $times;
    }

    /**
     * Get Bad Times
     *
     * @param $place_id
     * @param $people
     * @param $times
     * @param $capacityOnPlace
     * @param $date
     * @return array|JsonResponse
     */
    public function getBadTimes($place_id, $people, $times, $capacityOnPlace, $date)
    {
        $index = 0;
        $bad_times = [];

        foreach ($times as $time) {
            $peoples = Order::findOrFail($place_id)
                ->where('date', $date)
                ->where('time', '<=', Carbon::parse($time)->format('G:i:s'))
                ->where('staying_end', '>', Carbon::parse($time)->format('G:i:s'))
                ->get('people');
            $capacity = $peoples->sum('people');
            if (($capacity + $people) > $capacityOnPlace)
                $bad_times[] = $times[array_search($time, $times)];
        }

        return $bad_times;
    }

    /**
     * Get Available Times
     *
     * @param $bad_times
     * @param $times
     * @return mixed
     */
    public function getAvailableTimes($bad_times, $times)
    {
        return array_values(array_diff($times, $bad_times));
    }
}
