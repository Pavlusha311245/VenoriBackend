@extends('layouts.app')
@section('title', 'Places')
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
    <div class="container">
        <h1 style="text-align: center">Places</h1>
        @foreach($places as $place)
            <div class="accordion accordion-flush" id="accordionFlushExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="flush-headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseOne" aria-expanded="false"
                                aria-controls="flush-collapseOne"
                                style="background-image: url({{asset($place->image_url)}}); background-repeat: no-repeat;
                                    background-position: center; background-size: contain">
                            {{$place->name}}
                        </button>
                    </h2>
                    <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne"
                         data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Rating
                                    <span class="badge badge-primary badge-pill">{{$place->rating}}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Capacity
                                    <span class="badge badge-primary badge-pill">{{$place->capacity}}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Address
                                    <span class="badge badge-primary badge-pill">{{$place->address_full}}</span>
                                </li>
                            </ul>
                            @foreach($place['menu'] as $category => $products)
                                <div>
                                    <h3>{{$category}}</h3>
                                    <ul class="list-group">
                                        @foreach($products as $product)
                                            <li class="list-group-item"><img src="{{asset($product->image_url)}}"
                                                                             width="30"
                                                                             height="30"/><a
                                                    href="/admin/products/{{$product->id}}"
                                                    style="padding: 10px; color: #6cb2eb; text-decoration: none">{{$product->name}}</a>
                                            </li>
                                            <ul class="list-group">
                                                <li class="list-group-item">Weight: {{$product->weight}}</li>
                                                <li class="list-group-item">Price: {{$product->price}}</li>
                                            </ul>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div style="text-align: center; background: white; padding: 5px"><a href="/admin/places/create"><img
                    src="https://img.icons8.com/nolan/64/plus.png" width="30" height="30"/></a></div>
    </div>
@endsection
