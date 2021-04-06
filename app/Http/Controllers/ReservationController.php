<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationTimeRequest;
use App\Models\Order;
use App\Models\Place;
use App\Services\ReservationService;
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
        $times = $this->reservation_service->getTimes($place_id);
        $bad_times = $this->reservation_service->getBadTimes($place_id, $request->people, $request->staying, $times);
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

        $staying_end = date('Y-m-d H:i:s', strtotime($request->datetime) + ($request->staying * 60));

        $order = Order::create([
            'status' => 'In Progress',
            'price' => $this->price,
            'datetime' => $request['datetime'],
            'people' => $request['people'],
            'staying' => $request['staying'],
            'staying_end' => $staying_end,
            'user_id' => auth()->user()->id,
            'place_id' => $place_id,
        ]);

        return response()->json($order, 200);
    }
}
