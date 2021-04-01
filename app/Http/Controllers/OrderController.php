<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getBookingHistory()
    {
        return Order::where('user_id', auth()->user()->id)->where('status', 'Confirmed')->paginate(5);
    }
}
