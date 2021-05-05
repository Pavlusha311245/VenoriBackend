<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

/**
 * Class OrderController for CRUD Orders
 *
 * @package App\Http\Controllers
 */
class OrderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Orders history",
     *     description="Getting booking history of orders",
     *     operationId="ordersGetBookingHistory",
     *     tags={"orders"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success getting booking history of orders",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Order")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthenticated.")
     *          )
     *     )
     * )
     */
    public function getBookingHistory()
    {
        $this->updateOrders();

        return Order::where('user_id', auth()->user()->id)
            ->whereIn('status', ['Rejected', 'Confirmed'])
            ->orderBy('updated_at', 'desc')
            ->paginate(Config::get('constants.pagination.count'));
    }

    /**
     * @OA\Get(
     *     path="/api/booking_history",
     *     summary="Order info",
     *     description="Getting active orders",
     *     operationId="ordersGetBookingHistory",
     *     tags={"orders"},
     *     security={ {"bearer": {} }},
     *     @OA\Response(
     *          response=200,
     *          description="Success getting active orders",
     *          @OA\JsonContent(
     *              @OA\Property(property="current_page", type="integer", example=1),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(type="object", ref="#/components/schemas/Order")
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthenticated.")
     *          )
     *     )
     * )
     */
    public function getActiveOrders()
    {
        $this->updateOrders();

        return Order::where('user_id', auth()->user()->id)
            ->where('status', 'In Progress')
            ->paginate(Config::get('constants.pagination.count'));
    }

    /**
     * @OA\Post(
     *     path="/api/orders/{order_id}",
     *     summary="Cancel order",
     *     description="Cancelling order",
     *     operationId="ordersCancel",
     *     tags={"orders"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="ID of order",
     *          in="path",
     *          name="id",
     *          required=true,
     *          example=1,
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success cancelling place",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Order is canceled successfully")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Order not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="No order found")
     *          )
     *     )
     * )
     */
    public function cancelOrder($order_id)
    {
        Order::findOrFail($order_id)
            ->where('user_id', auth()->user()->id)
            ->where('id', $order_id)
            ->update(['status' => 'Rejected']);
        return response()->json(['message' => 'Order is canceled successfully']);
    }

    private function updateOrders()
    {
        return Order::where('date', '<=', Carbon::now()->toDateString())
            ->where('staying_end', '<', Carbon::now()->format('g:i A'))
            ->where('status', 'In Progress')
            ->update(['status' => 'Confirmed']);
    }
}
