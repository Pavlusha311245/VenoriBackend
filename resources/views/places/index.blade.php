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
        <div class="accordion" id="accordionPanelsStayOpenExample">
            @foreach($places as $place)
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
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Rating <img
                                            src="{{asset('/img/star.svg')}}"
                                            width="15" height="15"></span>
                                    <span class="badge bg-primary rounded-pill">{{$place->rating}}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Capacity <img
                                            src="{{asset('/img/people.svg')}}"
                                            width="15" height="15"></span>
                                    <span class="badge bg-primary rounded-pill">{{$place->capacity}}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Address <img
                                            src="{{asset('/img/address.svg')}}"
                                            width="15" height="15"></span>
                                    <span class="badge bg-primary rounded-pill">{{$place->address_full}}</span>
                                </li>
                            </ul>
                            @if($place['menu'] == [])
                                <div style="text-align: center"><strong>No products</strong></div>
                            @else
                                @foreach($place['menu'] as $category => $products)
                                    <div>
                                        <h3>{{$category}}</h3>
                                        <div class="row">
                                            @foreach($products as $product)
                                                <div class="col-sm"><img src="{{asset($product->image_url)}}"
                                                                         width="30"
                                                                         height="30" style="margin: 10px"/><a
                                                        href="/admin/products/{{$product->id}}"
                                                        style="padding: 10px; color: #6cb2eb; text-decoration: none">
                                                        {{$product->name}}</a>
                                                    <ul class="list-group">
                                                        <li class="list-group-item">Weight: {{$product->weight}}</li>
                                                        <li class="list-group-item">Price: {{$product->price}}</li>
                                                    </ul>
                                                    <div class="btn-group" role="group" style="padding: 10px">
                                                        <a href="/admin/products/{{$product->id}}/edit"
                                                           class="btn btn-sm btn-primary">
                                                            <img
                                                                src="{{asset('/img/refresh.svg')}}"
                                                                width="15" height="15"/>
                                                        </a>
                                                        <form id="deleteProduct{{$product->id}}" method="post" action="/admin/places/{{$place->id}}/products/{{$product->id}}/delete">
                                                        <button type="submit"
                                                            form="deleteProduct{{$product->id}}"
                                                            class="btn btn-sm btn-danger">
                                                            <img
                                                                src="{{asset('/img/rubbish-bin.svg')}}"
                                                                width="15" height="15"/>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="text-align: center; background: white; padding: 5px"><a href="/admin/places/create"><img
                    src="https://img.icons8.com/nolan/64/plus.png" width="30" height="30"/></a></div>
    </div>
@endsection
