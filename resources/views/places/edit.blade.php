@extends('layouts.app')
@section('title', 'Edit Place')
@section('content')
    <div class="d-flex justify-content-center">
        <div class="whiteBlockPurpleBorder">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h1 style="text-align: center"> Edit place №{{$place->id}}</h1>
            <div class="form-group">
                {!! Form::open(['action' => ['App\Http\Controllers\PlaceController@edit', $place->id], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
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
                    {{Form::number('capacity', $place->capacity, ['class' => 'form-control', 'placeholder' => 'Capacity', 'min' => 0])}}
                </div>
                <div class="form-row">
                    {{Form::label('table_price', 'Table Price')}}
                    {{Form::number('table_price', $place->table_price, ['class' => 'form-control', 'placeholder' => 'Table Price', 'min' => 0])}}
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
                    {{Form::number('address_lat', number_format($place->address_lat, 15), ['class' => 'form-control', 'placeholder' => 'Address Lat', 'step'=>'0.000000000000001'])}}
                </div>
                <div class="form-row">
                    {{Form::label('address_lon', 'Address Lon')}}
                    {{Form::number('address_lon', number_format($place->address_lon, 15), ['class' => 'form-control', 'placeholder' => 'Address Lon', 'step'=>'0.000000000000001'])}}
                </div>
                <div class="form-row">
                    {{Form::label('image', 'Image')}}
                    {{Form::file('image', ['class' => 'form-control', 'name' => 'image', 'placeholder' => 'Image'])}}
                </div>
                <div class="d-flex justify-content-between">
                    <a href="/admin/places/{{$place->id}}" class="btn btn-secondary">Go back</a>
                    {{Form::submit('Update', ['class'=> 'btn btn-outline-primary'])}}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
