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
     * @OA\Get(
     *     path="/api/orders",
     *     summary="History",
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
     *                  @OA\Items(
     *                      type="object",
     *                      ref="#/components/schemas/Order"
     *                  ),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthorized"),
     *          )
     *     ),
     * )
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
     *                  @OA\Items(
     *                      type="object",
     *                      ref="#/components/schemas/Order"
     *                  ),
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthorized"),
     *          )
     *     ),
     * )
     */
    public function getActiveOrders()
    {
        $this->updateOrders();

        return Order::where('user_id', auth()->user()->id)
            ->where('status', 'In Progress')
            ->paginate(5);
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
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success cancelling place",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Order is canceled successfully")
     *          ),
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="Place not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="message", type="string", example="ModelNotFoundException handled for API")
     *          )
     *     ),
     * )
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
