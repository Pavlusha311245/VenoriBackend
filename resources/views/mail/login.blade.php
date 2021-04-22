@extends('layouts.mail')
@section('title', 'Login')
@section('content')
    <div class="main-block">
        <h1 style="text-align: center; color: #2d3748">New login</h1>
        <p style="color: white">The new entrance was made on {{date("d.m.Y")}} at {{date("H:i:s")}}</p>
    </div>
@endsection
