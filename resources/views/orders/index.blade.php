@extends('layouts.app')
@section('title', 'Orders')
@section('content')
    <style>
        table {
            border-collapse: collapse;
            border: 1px solid #9561e2;
        }

        select {
            margin: 0;
        }

        th, td {
            padding: 5px;
        }

        .table-row:not(:first-child) {
            transition: all 0.5s;
            padding: 1px;
            background-color: rgba(255, 255, 255, 0.5);
        }

        .orders:not(:first-child):hover {
            background-color: rgba(149, 97, 226, 0.5);
        }

        .update_form {
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #9561e2;
            background-color: rgba(255, 255, 255, 0.5);
        }
    </style>
    {{dd($orders)}}
    <div class="d-flex justify-content-center" style="height: 750px">
        <div style="height: min-content">
            @if(count($orders)==0)
                <h2>There is no data to form the table</h2>
                <p style="text-align: center"><a href="/admin/orders/create"><img
                            src="https://img.icons8.com/nolan/64/plus.png" width="50" height="50"/></a></p>
            @else
                <table style="margin: 100px 0; min-width: 100%;">
                    <tr style="background-color: rgba(122,117,226,0.5); text-align: center;">
                        <th style="width: 50px">Id</th>
                        <th>Status</th>
                        <th>Price</th>
                        <th>Date</th>
                        <th>People</th>
                        <th>Time</th>
                        <th>Details</th>
                        <th>Remove</th>
                    </tr>
                    @foreach($orders as $order)
                        <tr class="table-row">
                            <td style="text-align: center" id="order_id">{{$order['id']}}</td>
                            <td id="order_status">{{$order['status']}}</td>
                            <td id="order_price">{{$order['price']}}</td>
                            <td id="order_date">{{$order['date']}}</td>
                            <td id="order_people">{{$order['people']}}</td>
                            <td id="order_time">{{$order['time']}}-{{$order['staying_end']}}</td>
                            <td>
                                <a href="/admin/orders/{{$order->id}}" class="btn btn-outline-primary btn-sm">Show</a>
                            </td>
                            <td>
                                <a href="/admin/orders/{{$order->id}}/delete" class="btn btn-danger btn-sm">Remove</a>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                </table>
        </div>
    </div>

@endsection
