@extends('layouts.app')
@section('title', 'Edit Place')
@section('content')
    <div class="d-flex justify-content-center">
        <div class="whiteBlockPurpleBorder">
            <h1 style="text-align: center"> Edit place №{{$place->id}}</h1>
            <div class="form-group">
                {!! Form::open(['action' => ['App\Http\Controllers\PlaceController@edit', $place->id], 'method' => 'POST']) !!}
                @csrf
                <div class="form-row">
                    {{Form::label('name', 'Name')}}
                    {{Form::text('name', $place->name, ['class' => 'form-control', 'placeholder' => 'Name'])}}
                </div>
                <div class="form-row">
                    {{Form::label('type', 'Type')}}
                    {{Form::text('type', $place->type, ['class' => 'form-control', 'placeholder' => 'Type'])}}
                </div>
                <div class="form-row">
                    {{Form::label('phone','Phone')}}
                    {{Form::tel('phone', $place->phone, ['class' => 'form-control', 'placeholder' => 'Phone'])}}
                </div>
                <div class="form-row">
                    {{Form::label('capacity','Capacity')}}
                    {{Form::number('capacity', $place->capacity, ['class' => 'form-control', 'placeholder' => 'Capacity'])}}
                </div>
                <div class="form-row">
                    {{Form::label('table_price', 'Table Price')}}
                    {{Form::number('table_price', $place->table_price, ['class' => 'form-control', 'placeholder' => 'Table Price'])}}
                </div>
                <div class="form-row">
                    {{Form::label('description', 'Description')}}
                    {{Form::text('description', $place->description, ['class' => 'form-control', 'placeholder' => 'Description'])}}
                </div>
                <div class="form-row">
                    {{Form::label('address_full', 'Address Full')}}
                    {{Form::text('address_full', $place->address_full, ['class' => 'form-control', 'placeholder' => 'Address Full'])}}
                </div>
                <div class="form-row">
                    {{Form::label('address_lat', 'Address Lat')}}
                    {{Form::number('address_lat', $place->address_lat, ['class' => 'form-control', 'placeholder' => 'Address Lat'])}}
                </div>
                <div class="form-row">
                    {{Form::label('address_lon', 'Address Lon')}}
                    {{Form::number('address_lon', $place->address_lon, ['class' => 'form-control', 'placeholder' => 'Address Lon'])}}
                </div>
                <div class="form-row">
                    {{Form::label('image', 'Image')}}
                    {{Form::file('image')}}
                </div>
                <div class="d-flex justify-content-between">
                    <a href="/admin/places/{{$place->id}}" class="btn btn-secondary">Go back</a>
                    {{Form::submit('Update',['class'=> 'btn btn-outline-primary'])}}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
