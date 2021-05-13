@extends('layouts.app')
@section('title','Product details')
@section('content')
    <div class="d-flex justify-content-center">
        <div class="whiteBlockPurpleBorder">
            @if(session('message'))
                <div class="alert alert-success" style="margin-top: 20px">{{session('message')}}</div>
            @endif
            <h1 style="text-align: center"> Product №{{$products->id}} <img src="{{asset($products->image_url)}}" width="45" height="45" style="border-radius: 5px"/></h1>
            <div class="d-flex row" style="font-weight: normal; font-style: italic">
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>Name</div>
                    <div>{{$products->name}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>Weight</div>
                    <div>{{$products->weight}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>Price</div>
                    <div>{{$products->price}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>CategoryId</div>
                    <div>{{$products->category_id}}</div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="/admin/places/" class="btn btn-secondary" style="width: 100px">Go back</a>
                    <a href="/admin/products/{{$products->id}}/edit" class="btn btn-primary">Update</a>
                </div>
            </div>
        </div>
    </div>
@endsection
