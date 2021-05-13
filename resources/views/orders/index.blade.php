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

        .places:not(:first-child):hover {
            background-color: rgba(149, 97, 226, 0.5);
        }

        .update_form {
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #9561e2;
            background-color: rgba(255, 255, 255, 0.5);
        }
    </style>
    <div class="container" style="margin: 100px auto">
        @if(session('message'))
            <div class="alert alert-success" style="margin-top: 20px">{{session('message')}}</div>
        @endif
        <h1 style="text-align: center">Orders</h1>
        <div class="accordion" id="accordionPanelsStayOpenExample">
            {{--            {{dd($places->get()[0]->orders()->where('status', 'In Progress')->get())}}--}}
            @foreach($places->get() as $place)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="panelsStayOpen-heading{{$place->id}}">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#panelsStayOpen-collapse{{$place->id}}" aria-expanded="true"
                                aria-controls="panelsStayOpen-collapse{{$place->id}}">
                            {{$place->name}}
                        </button>
                    </h2>
                    <div id="panelsStayOpen-collapse{{$place->id}}" class="accordion-collapse collapse show"
                         aria-labelledby="panelsStayOpen-heading{{$place->id}}">
                        <div class="accordion-body">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col">Order ID</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Client</th>
                                    <th scope="col">Client ID</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($place->orders()->get() as $order)
                                    @switch($order->status)
                                        @case('In Progress')
                                        <tr class="table-primary">
                                        @break
                                        @case('Confirmed')
                                        <tr class="table-success">
                                        @break
                                        @case('Rejected')
                                        <tr class="table-danger">
                                            @break
                                            @endswitch
                                            <th scope="row">{{$order->id}}</th>
                                            <td>{{$order->status}}</td>
                                            <td>{{$order->price}}</td>
                                            <td>{{$order->date}}</td>
                                            <td>{{$order->time}}-{{$order->staying_end}}</td>
                                            <td>{{$order->user()->first()->first_name}} {{$order->user()->first()->second_name}}</td>
                                            <td>{{$order->user()->first()->id}}</td>
                                        </tr>
                                        @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

