<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Place;
use DateTime;
use Exception;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use function Illuminate\Events\queueable;

/**
 * Class ReservationController
 * @package App\Http\Controllers
 */
class ReservationController extends Controller
{
    /**
     * Get array of Times
     * @param $place_id
     * @return array
     */
    public function GetTimes($place_id)
    {
        $place = Place::findOrFail($place_id); //PLACE 2
        $work_start = $place->work_start; //17:30
        $work_end = $place->work_end; //19:00
        $startTime = strtotime($work_start);
        $endTime = strtotime($work_end);
        $returnTimeFormat = ('12') ? 'g:i A' : 'G:i';

        $current = time();
        $addTime = strtotime('+' . '30 mins', $current);
        $diff = $addTime - $current;

        $times = array();
        while ($startTime < $endTime) {
            $times[] = date($returnTimeFormat, $startTime);
            $startTime += $diff;
        }
        $times[] = date($returnTimeFormat, $startTime);
        return $times;
    }

    /**
     * @param $place_id
     * @return array|false|JsonResponse|string
     */
    public function AvailableTime($place_id)
    {
        $times = $this->GetTimes($place_id); //19:00 - 21:00 / 30min
        $result_times = array();
        $bad_times = array();

        $datetime = '2021-04-01 19:00:00'; //INPUT DATETIME

        //19:00 - 20:00 / 3 cheloveka / !SPECIAL / 2 chasa

        $time = date("g:i A", strtotime($datetime));

        if (!in_array($time, $times)) {
            return response()->json(['message' => 'Incorrect Datetime']);
        }

        $people = 13;   //INPUT NUMBER OF ADULTS
        $capacity = Place::findOrFail($place_id)->capacity;

        $special = 0;

        if ($special == 1) {
            return response()->json(['message' => 'It it special event']);
        }

        $staying = 1;

        $k = $staying + 0.5;
        $i = 0;

//vmesto count vernut people i poschitat

        while ($k != 0) {
            $time = date('G:i', strtotime($times[$i]));
            $capacity_on_date = Order::where('place_id', $place_id)->where("datetime", "LIKE", "%" . $time . "%")->count();

            if (($capacity_on_date + $people) > $capacity) {
                $bad_times[] = $times[array_search($times[$i], $times)];
            }

            $k -= 0.5;
            $i++;
        }
        // $indexes = /0/1/2
        // $times = 19:00 19:30 20:00 20:30 21:00 21:30 22:00
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

    public function Reserve()
    {
        //
    }
}
