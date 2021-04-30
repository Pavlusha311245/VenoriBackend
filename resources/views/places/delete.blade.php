@extends('layouts.app')
@section('title','Place Remove')
@section('content')
    <div class="d-flex justify-content-center align-items-center" style="height: 500px">
        <div class="whiteBlockPurpleBorder">
            <h2 style="text-align: center"> Are you sure you want to delete the place №{{$place->id}}? </h2>
            <div class="d-flex column justify-content-between">
                <a href="/admin/places" class="btn btn-secondary" style="width: 100px">Cancel</a>
                {!! Form::open(['action' => ['App\Http\Controllers\PlaceController@remove', $place->id], 'method' => 'POST']) !!}
                @csrf
                {{Form::submit('Yes',['class'=> 'btn btn-danger'])}}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
