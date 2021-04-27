<?php

namespace App\Services;

use App\Models\Order;
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
        $startTime = strtotime($work_start);
        $endTime = strtotime($work_end);

        $returnTimeFormat = ('12') ? 'g:i A' : 'G:i';
        $current = time();
        $addTime = strtotime('+' . '30 mins', $current);
        $diffTimes = $addTime - $current;

        $times = [];
        while ($startTime < $endTime) {
                $times[] = date($returnTimeFormat, $startTime);
                $startTime += $diffTimes;
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

        while ($index < count($times)) {
            $peoples = Order::where('place_id', $place_id)
                ->where('date', $date)
                ->where('time', '<=', date('G:i:s', strtotime($times[$index])))
                ->where('staying_end', '>', date('G:i:s', strtotime($times[$index])))
                ->get('people');
            $capacity = $peoples->sum('people');
            if (($capacity + $people) > $capacityOnPlace)
                $bad_times[] = $times[array_search($times[$index], $times)];

            $index++;
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
        if (!empty($bad_times))
            foreach ($bad_times as $bad)
                foreach ($times as $key => $time)
                    if ($bad == $time)
                    {
                        unset($times[$key]);
                        $times = array_values($times);
                    }

        return $times;
    }
}
