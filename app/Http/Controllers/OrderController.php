<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriesRequest;
use App\Models\Order;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    /**
     * Get booking history of Orders
     * @return mixed
     */
    public function getBookingHistory()
    {
        return Order::where('user_id', auth()->user()->id)->where('status', 'Rejected')->orderBy('updated_at', 'desc')->paginate(5);
    }

    /**
     * Get active Orders
     * @return JsonResponse
     */
    public function getActiveOrders()
    {
        return Order::where('user_id', auth()->user()->id)->where('status', 'Confirmed', 'In Progress')->paginate(5);
    }

    /**
     * Store of Orders
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function store(Request $request)
    {
        $category = Order::create($request->all());

        return response($category, 201);
    }

    /**
     * Show of Orders
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        return Order::findOrFail($id);
    }

    /**
     * Update Orders
     * @param CategoriesRequest $request
     * @param Order $order
     * @return JsonResponse
     */
    public function update(CategoriesRequest $request, Order $order)
    {
        $order->update($request->all());

        return response()->json(['message' => 'Order Is Updated Successfully'], 200);
    }

    /**
     * Destroy Orders
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $category = Order::findOrFail($id);
        $category->products()->delete();
        $category->delete();
        return response()->json(['message' => 'Category is deleted successfully'], 200);
    }
}
