@extends('layouts.app')
@section('title','User details')
@section('content')
    <div class="d-flex justify-content-center">

        <div class="whiteBlockPurpleBorder">
            @if(session('message'))
                <div class="alert alert-success" style="margin-top: 20px">{{session('message')}}</div>
            @endif
            <h1 style="text-align: center"> User №{{$user->id}} <img src="{{asset($user->avatar)}}" width="45"
                                                                     height="45" style="border-radius: 5px"/></h1>
            <div class="d-flex row" style="font-weight: normal; font-style: italic">
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>Name</div>
                    <div>{{$user->first_name}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>Surname</div>
                    <div>{{$user->second_name}}</div>
                </div>
                <div class="d-flex column justify-content-between" style="margin-bottom: 15px">
                    <div>Email</div>
                    <div>{{$user->email}}</div>
                </div>
                <div class="d-flex justify-content-between" style="margin-bottom: 15px">
                    <div>Role</div>
                    <div>
                        @foreach($user->roles as $role)
                            <span style="background-color: #7cffb4; padding: 5px; border-radius: 5px">{{$role['name']}}</span>
                        @endforeach
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="/admin/users" class="btn btn-secondary" style="width: 100px">Go back</a>
                    <a href="/admin/users/{{$user->id}}/edit" class="btn btn-primary">Update</a>
                </div>
            </div>
        </div>
    </div>
@endsection
