@extends('layouts.mail')
@section('title', 'Forgot password')
@section('content')
    <div class="main-block" style="text-align: center">
        <h1 style="color: #2d3748">Your token for reset password</h1>
        <p style="color: white">{{$token}}</p>
    </div>
@endsection
