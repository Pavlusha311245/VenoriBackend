<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationTimeRequest;
use App\Models\Order;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;

/**
 * Class ReservationController
 * @package App\Http\Controllers
 */
class ReservationController extends Controller
{
    protected $reservation_service;
    protected $price = 40;

    /**
     * ReservationService constructor.
     * @param ReservationService $reservation_service
     */
    public function __construct(ReservationService $reservation_service)
    {
        $this->reservation_service = $reservation_service;
    }

    /**
     * Get array of Available Times
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
     * @param ReservationTimeRequest $request
     * @param $place_id
     * @return JsonResponse
     */
    public function tableReserve(ReservationTimeRequest $request, $place_id)
    {
        $order = Order::create([
            'status' => 'Confirmed',
            'price' => $this->price,
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
