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

    <div class="d-flex justify-content-center" style="height: 750px">
        <div style="height: min-content">
            @if(count($places)==0)
                <h2>There is no data to form the table</h2>
            @else
                <table style="margin: 100px 0; min-width: 100%;">
                    <tr style="background-color: rgba(122,117,226,0.5); text-align: center;">
                        <th style="width: 50px">Id</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Rating</th>
                        <th>Phone</th>
                        <th>Capacity</th>
                        <th>Table Price</th>
                        <th>Description</th>
                        <th>Address Full</th>
                        <th>Address Lat</th>
                        <th>Address Lon</th>
                        <th>Image Url</th>
                        <th>Details</th>
                        <th>Remove</th>
                    </tr>
                    @foreach($places as $place)
                        <tr class="table-row">
                            <td style="text-align: center" id="place_id">{{$place['id']}}</td>
                            <td id="place_name">{{$place['name']}}</td>
                            <td id="place_type">{{$place['type']}}</td>
                            <td id="place_rating">{{$place['rating']}}</td>
                            <td id="place_phone">{{$place['phone']}}</td>
                            <td id="place_capacity">{{$place['capacity']}}</td>
                            <td id="place_price">{{$place['table_price']}}</td>
                            <td id="place_description">{{$place['description']}}</td>
                            <td id="place_address_full">{{$place['address_full']}}</td>
                            <td id="place_address_lat">{{$place['address_lat']}}</td>
                            <td id="place_address_lon">{{$place['address_lon']}}</td>
                            <td id="place_image_url">{{$place['image_url']}}</td>
                            <td>
                                <a href="/admin/places/{{$place->id}}" class="btn btn-outline-primary btn-sm">Show</a>
                            </td>
                            <td>
                                <a href="/admin/places/{{$place->id}}/delete" class="btn btn-danger btn-sm">Remove</a>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="table-row">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: center"><a href="/admin/places/create"><img src="https://img.icons8.com/nolan/64/plus.png" width="30" height="30"/></a></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endif
                </table>
        </div>
    </div>

@endsection
