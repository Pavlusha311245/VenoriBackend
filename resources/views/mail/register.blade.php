@extends('layouts.mail')
@section('title', 'Register')
@section('content')
    <div class="main-block">
        <h1 style="text-align: center; color: #2d3748">Hello, {{$user->first_name}}</h1>
        <p style="color: white">We are the Venori team. We are glad that you have chosen us. Thank you! You can tell
            your friends and acquaintances about us so that our service becomes better and better.</p>
    </div>
@endsection
