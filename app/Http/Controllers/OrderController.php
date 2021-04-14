<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

/**
 * Class OrderController for CRUD Orders
 *
 * @package App\Http\Controllers
 */
class OrderController extends Controller
{
    /**
     * Get booking history of Orders
     *
     * @return mixed
     */
    public function getBookingHistory()
    {
        $this->updateOrders();

        return Order::where('user_id', auth()->user()->id)
            ->whereIn('status', ['Rejected', 'Confirmed'])
            ->orderBy('updated_at', 'desc')
            ->paginate(5);
    }

    /**
     * Get active Orders
     *
     * @return JsonResponse
     */
    public function getActiveOrders()
    {
        $this->updateOrders();

        return Order::where('user_id', auth()->user()->id)
            ->where('status', 'In Progress')
            ->paginate(5);
    }

    /**
     * Cancel Orders
     *
     * @param $order_id
     * @return mixed
     */
    public function cancelOrder($order_id)
    {
        return Order::findOrFail($order_id)
            ->where('user_id', auth()->user()->id)
            ->where('id', $order_id)
            ->update(['status' => 'Rejected']);
    }

    public function updateOrders()
    {
        return Order::where('staying_end', '<', date('Y-m-d H:i:s', strtotime(Carbon::now())))
            ->where('status', 'In Progress')
            ->update(['status' => 'Confirmed']);
    }
}
