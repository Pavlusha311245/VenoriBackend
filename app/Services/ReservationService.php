<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Place;

/**
 * Class ReservationService
 * @package App\Http\Services
 */
class ReservationService
{
    /**
     * Get array of Times
     * @param $place_id
     * @return array
     */
    public function getTimes($place_id)
    {
        $place = Place::findOrFail($place_id);

        $work_start = $place->work_start;
        $work_end = $place->work_end;
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
        $times[] = date($returnTimeFormat, $startTime);

        return $times;
    }

    /**
     * Get Bad Times
     * @param $staying
     * @param $place_id
     * @param $people
     * @param $times
     * @return array
     */
    public function getBadTimes($place_id, $people, $staying, $times)
    {
        $half = 0.5;
        $capacity_place = Place::findOrFail($place_id)->capacity;
        $index = 0;
        $qt_of_cycles = $staying;
        $bad_times = [];

        while ($qt_of_cycles != 0)
        {
            $time = date('G:i', strtotime($times[$index]));
            $capacity_time = Order::findOrFail($place_id)->where('datetime', 'LIKE', '%' . $time . '%')->get('people');
            $capacity = array_sum(array_column(json_decode($capacity_time), 'people'));

            if (($capacity + $people) > $capacity_place) {
                $bad_times[] = $times[array_search($times[$index], $times)];
            }

            $qt_of_cycles -= $half;
            $index++;
        }

        return $bad_times;
    }

    /**
     * Get Available Times
     * @param $bad_times
     * @param $times
     * @return mixed
     */
    public function getAvailableTimes($bad_times, $times)
    {
        if (!empty($bad_times)) {
            foreach ($bad_times as $bad) {
                foreach ($times as $key => $time) {
                    if ($bad == $time) {
                        unset($times[$key]);
                        $times = array_values($times);
                    }
                }
            }
        }
        $result_times[] = $times;

        return $result_times;
    }
}
