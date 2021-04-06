<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

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
        //otmena broni
    //perenos active -> v history
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories',
        ]);

        if ($validator->fails()) {
            return response(['error' => $validator->errors(), 'Validation Error']);
        }

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
        try {
            return Order::findOrFail($id);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Category Is Not Found'], 201);
        }
    }

    /**
     * Update Orders
     * @param Request $request
     * @param Order $order
     * @return JsonResponse
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'name' => 'required|unique:categories',
        ]);

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
        try {
            $category = Order::findOrFail($id);
            $category->products()->delete();
            $category->delete();
            return response()->json(['message' => 'Category is deleted successfully'], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Category Is Not Found'], 201);
        }
    }
}
