<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Class ReservationService
 * @package App\Http\Services
 */
class ReservationService
{
    public $half = 0.5;
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
        $diff = $addTime - $current;

        $times = [];
        while ($startTime < $endTime) {
            $times[] = date($returnTimeFormat, $startTime);
            $startTime += $diff;
        }
        $times[] = date($returnTimeFormat, $startTime);

        return $times;
    }

    public function isDateTimeCorrect()
    {

    }

    /**
     * @param Request $request
     * @param $place_id
     * @param $half
     * @return array|false|JsonResponse|string
     */
    public function availableTime(Request $request, $place_id, $half)
    {
        $request->validate([

        ]);

        $times = $this->GetTimes($place_id);
        $result_times = [];
        $bad_times = [];

        $datetime = $request->datetime;

        $date = date("d-m-Y", strtotime($datetime));
        $time = date("g:i A", strtotime($datetime));

        if (!in_array($time, $times)) {
            return response()->json(['message' => 'Incorrect time']);
        }

        $people = $request->people;
        if ($people == 0) {
            return response()->json(['message' => 'Incorrect people']);
        } else {
            $capacity_place = Place::findOrFail($place_id)->capacity;
            $special = $request->special;

            if ($special == 0) {
                $staying = $request->staying;

                if ($date = Order::where('place_id', $place_id)->where("datetime", "LIKE", "%" . $date . "%")->get()) {
                    $k = $staying + $half;
                    $i = 0;

                    while ($k != 0) {
                        $time = date('G:i', strtotime($times[$i]));
                        $capacity_time = Order::where('place_id', $place_id)->where("datetime", "LIKE", "%" . $time . "%")->get('people');
                        $capacity = array_sum(array_column(json_decode($capacity_time), 'people'));

                        if (($capacity + $people) > $capacity_place) {
                            $bad_times[] = $times[array_search($times[$i], $times)];
                        }

                        $k -= $half;
                        $i++;
                    }
                }

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
            else {
                return response()->json(['message' => 'It is a Special Event. Please, call by phone: +123456789']);
            }
        }
    }

    /**
     * @param Request $request
     * @param $place_id
     * @return JsonResponse
     */
    public function TableReserve(Request $request, $place_id)
    {
        $request->validate([
            'datetime' => 'required',
            'people' => 'required',
            'special' => 'required',
            'staying' => 'required',
        ]);

        $price = 40;

        $order = Order::create([
            'status' => 'Confirmed',
            'price' => $price,
            'datetime' => $request['datetime'],
            'people' => $request['people'],
            'special' => $request['special'],
            'staying' => $request['staying'],
            'user_id' => auth()->user()->id,
            'place_id' => $place_id,
        ]);

        return response()->json($order, 200);
    }
}
