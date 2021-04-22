@extends('layouts.mail')
@section('title', 'Confirm order')
@section('content')
    <style>
        .detail {
            color: #2d3748;
        }
    </style>
    <div class="main-block">
        <h1 style="text-align: center; color: #38c172">Your order is accepted.</h1>
        <div style="color: white">
            <h2>Order details:</h2>
            <p class="detail">Price: {{$order->price}}</p>
            <p class="detail">People: {{$order->people}}</p>
            <p class="detail">Date: {{$order->date}}</p>
            <p class="detail">Time: {{$order->time}} - {{$order->staying_end}}</p>
        </div>
    </div>
@endsection
