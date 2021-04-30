@extends('layouts.app')
@section('title','User details')
@section('content')
    <div class="d-flex justify-content-center align-items-center" style="height: 500px">
        <div class="whiteBlockPurpleBorder">
            <h2 style="text-align: center"> Are you sure you want to delete the user №{{$user->id}}? <img src="{{asset($user->avatar)}}" width="45" height="45"
                                                                     style="border-radius: 5px"/></h2>
            <div class="d-flex column justify-content-between">
                    <a href="/admin/users" class="btn btn-secondary" style="width: 100px">Cancel</a>
                    {!! Form::open(['action' => ['App\Http\Controllers\UserController@remove', $user->id], 'method' => 'POST']) !!}
                    @csrf
                    {{Form::submit('Yes',['class'=> 'btn btn-danger'])}}
                    {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
