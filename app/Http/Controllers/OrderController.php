<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriesRequest;
use App\Models\Order;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        return Order::where('user_id', auth()->user()->id)->where('status', 'Rejected')->orderBy('updated_at', 'desc')->paginate(5);
    }

    /**
     * Get active Orders
     *
     * @return JsonResponse
     */
    public function getActiveOrders()
    {
        return Order::where('user_id', auth()->user()->id)->where('status', 'Confirmed', 'In Progress')->paginate(5);
    }
}
