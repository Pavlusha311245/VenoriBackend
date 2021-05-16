<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;
use Carbon\CarbonInterval;

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
     * @return array
     */
    public function getBadTimes($place_id, int $people, $times, $capacityOnPlace, $date)
    {
        $bad_times = [];

        foreach ($times as $time) {
            $peoples = Order::where('place_id', $place_id)
                ->where('date', $date)
                ->where('time', '<=', Carbon::parse($time)->format('H:i'))
                ->where('staying_end', '>', Carbon::parse($time)->format('H:i'))
                ->sum('people');

            if (($peoples + $people) > $capacityOnPlace)
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
