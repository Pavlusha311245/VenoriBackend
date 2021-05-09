@extends('layouts.app')
@section('title','Place details')
@section('content')
    <div class="d-flex justify-content-center">
        <div class="whiteBlockPurpleBorder">
            <div>
                <h1 style="width: 100%; text-align: center; color: black; border-radius: 10px; padding: 10px 0 0 0; margin: 0"> Place №{{$place->id}} </h1>
                <img src="{{asset($place->image_url)}}" width="100%" height="auto" style="border-radius: 10px;"/>
            </div>
            <div class="d-flex row" style="font-weight: normal; font-style: italic">
                <div class="d-flex column justify-content-between" style="margin: 5px 0">
                    <div>Name</div>
                    <div>{{$place->name}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin: 5px 0">
                    <div>Type</div>
                    <div>{{$place->type}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin: 5px 0">
                    <div>Rating</div>
                    <div>{{$place->rating}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin: 5px 0">
                    <div>Phone</div>
                    <div>{{$place->phone}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin: 5px 0">
                    <div>Capacity</div>
                    <div>{{$place->capacity}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin: 5px 0">
                    <div>Table Price</div>
                    <div>{{$place->table_price}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin: 5px 0">
                    <div>Description</div>
                    <div>{{$place->description}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin: 5px 0">
                    <div>Address Full</div>
                    <div>{{$place->address_full}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin: 5px 0">
                    <div>Address Lat</div>
                    <div>{{number_format($place->address_lat, 15)}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin: 5px 0">
                    <div>Address Lon</div>
                    <div>{{number_format($place->address_lon, 15)}}</div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="/admin/places/" class="btn btn-secondary" style="width: 100px">Go back</a>
                    <a href="/admin/places/{{$place->id}}/edit" class="btn btn-primary">Update</a>
                </div>
            </div>
        </div>
    </div>
@endsection
