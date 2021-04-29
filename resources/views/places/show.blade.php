@extends('layouts.app')
@section('title','Place details')
@section('content')
    <div class="d-flex justify-content-center">
        <div class="whiteBlockPurpleBorder">
            <h1 style="text-align: center"> Place №{{$place->id}} <img src="{{asset($place->image_url)}}" width="45" height="45" style="border-radius: 5px"/></h1>
            <div class="d-flex row" style="font-weight: normal; font-style: italic">
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>Name</div>
                    <div>{{$place->name}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>Type</div>
                    <div>{{$place->type}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>Rating</div>
                    <div>{{$place->rating}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>Phone</div>
                    <div>{{$place->phone}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>Capacity</div>
                    <div>{{$place->capacity}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>Table Price</div>
                    <div>{{$place->table_price}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>Description</div>
                    <div>{{$place->description}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>Address Full</div>
                    <div>{{$place->address_full}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>Address Lat</div>
                    <div>{{number_format($place->address_lat, 6)}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>Address Lon</div>
                    <div>{{number_format($place->address_lon, 6)}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>Image Url</div>
                    <div>{{$place->image_url}}</div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="/admin/places/" class="btn btn-secondary" style="width: 100px">Go back</a>
                    <a href="/admin/places/{{$place->id}}/edit" class="btn btn-primary">Update</a>
                </div>
            </div>
        </div>
    </div>
@endsection
