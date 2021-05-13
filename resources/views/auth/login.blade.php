@extends('layouts.app')
@section('title','Login')
@section('content')
    <div class="d-flex justify-content-center">
        <div class="whiteBlockPurpleBorder">
            @if(session('message'))
                <div class="alert alert-success" style="margin-top: 20px">{{session('message')}}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h2>Login</h2>
            {!! Form::open(['action' => ['App\Http\Controllers\Auth\AuthController@loginAdmin'], 'method' => 'POST']) !!}
            @csrf
            <div class="form-row">
                {{Form::label('email','Email')}}
                {{Form::text('email', '' ,['class' => 'form-control', 'placeholder' => 'Email'])}}
            </div>
            <div class="form-row">
                {{Form::label('password','Password')}}
                {{Form::password('password',['class' => 'form-control', 'placeholder' => 'Password'])}}
            </div>
            {{Form::submit('Login',['class'=> 'btn btn-success'])}}
            {!! Form::close() !!}
        </div>
    </div>
@endsection
