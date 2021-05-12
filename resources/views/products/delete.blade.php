@extends('layouts.app')
@section('title','Products remove')
@section('content')
    <div class="d-flex justify-content-center align-items-center" style="height: 500px">
        <div class="whiteBlockPurpleBorder">
            <h2 style="text-align: center"> Are you sure you want to delete the product №{{$product->id}}? <img src="{{asset($product->image_url)}}" width="45" height="45"
                                                                                                          style="border-radius: 5px"/></h2>
            <div class="d-flex column justify-content-between">
                <a href="/admin/place" class="btn btn-secondary" style="width: 100px">Cancel</a>
                {!! Form::open(['action' => ['App\Http\Controllers\ProductController@remove', $place_id, $product->id], 'method' => 'POST']) !!}
                @csrf
                {{Form::submit('Yes',['class'=> 'btn btn-danger'])}}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
