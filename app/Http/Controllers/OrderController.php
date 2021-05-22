<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaginateArrayService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\Request;

/**
 * Class OrderController for CRUD Orders
 *
 * @package App\Http\Controllers
 */
class OrderController extends Controller
{
    protected $arrayPaginator;

    public function __construct(PaginateArrayService $paginateArrayService)
    {
        $this->arrayPaginator = $paginateArrayService;
    }

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Order info",
     *     description="Getting orders by params",
     *     operationId="ordersGet",
     *     tags={"orders"},
     *     security={ {"bearer": {} }},
     *     @OA\Parameter(
     *          description="param active",
     *          in="path",
     *          name="active",
     *          required=false,
     *     ),
     *     @OA\Parameter(
     *          description="param history",
     *          in="path",
     *          name="history",
     *          required=false,
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success getting orders by params (example: active contains all orders with status In Progress)",
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
    public function getOrders(Request $request)
    {
        $this->updateOrders();

        $orders = Order::query();
        $orders->where('user_id', auth()->user()->id);

        if (!($request->has('history') && $request->has('active'))) {
            if ($request->has('history'))
                $orders->whereIn('status', ['Rejected', 'Confirmed']);

            if ($request->has('active'))
                $orders->where('status', 'In Progress');
        }

        $orders = $orders->get();

        foreach ($orders as $order) {
            $place = $order->place;
            $place['favourite'] = auth()->user()->favoutirePlaces()->find($place->id) !== null;
            $order['time'] = Carbon::parse($order['time'])->format('g:i A');
            $order['staying_end'] = Carbon::parse($order['staying_end'])->format('g:i A');
            $order['place'] = $place;
        }

        return $this->arrayPaginator->paginate($orders, Config::get('constants.pagination.count'));
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
        return Order::whereDate('date', '<=', Carbon::now()->toDateString())
            ->where('staying_end', '<', Carbon::now()->format('H:i'))
            ->where('status', 'In Progress')
            ->update(['status' => 'Confirmed']);
    }
}
