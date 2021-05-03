@extends('layouts.app')
@section('title','Create Place')
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
            <h2>Create a new place</h2>
            {!! Form::open(['action' => ['App\Http\Controllers\PlaceController@create'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
            @csrf
            <div class="form-row">
                {{Form::label('name', 'Name')}}
                {{Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Name'])}}
            </div>
            <div class="form-row">
                {{Form::label('type', 'Type')}}
                {{Form::text('type', '', ['class' => 'form-control', 'placeholder' => 'Type'])}}
            </div>
            <div class="form-row">
                {{Form::label('phone','Phone')}}
                {{Form::tel('phone', '', ['class' => 'form-control', 'placeholder' => 'Phone'])}}
            </div>
            <div class="form-row">
                {{Form::label('capacity','Capacity')}}
                {{Form::number('capacity', '', ['class' => 'form-control', 'placeholder' => 'Capacity', 'min' => 0])}}
            </div>
            <div class="form-row">
                {{Form::label('table_price', 'Table Price')}}
                {{Form::number('table_price', '', ['class' => 'form-control', 'placeholder' => 'Table Price', 'min' => 0])}}
            </div>
            <div class="form-row">
                {{Form::label('description', 'Description')}}
                {{Form::text('description', '', ['class' => 'form-control', 'placeholder' => 'Description'])}}
            </div>
            <div class="form-row">
                {{Form::label('address_full', 'Address Full')}}
                {{Form::text('address_full', '', ['class' => 'form-control', 'placeholder' => 'Address Full'])}}
            </div>
            <div class="form-row">
                {{Form::label('address_lat', 'Address Lat')}}
                {{Form::number('address_lat', '', ['class' => 'form-control', 'placeholder' => 'Address Lat', 'step'=>'0.000000000000001'])}}
            </div>
            <div class="form-row">
                {{Form::label('address_lon', 'Address Lon')}}
                {{Form::number('address_lon', '', ['class' => 'form-control', 'placeholder' => 'Address Lon', 'step'=>'0.000000000000001'])}}
            </div>
            <div class="form-row">
                {{Form::label('image','Image')}}
                {{Form::file('image', ['class' => 'form-control', 'name' => 'image', 'placeholder' => 'Image'])}}
            </div>
            {{Form::submit('Create', ['class'=> 'btn btn-success btn-register'])}}
            {!! Form::close() !!}
        </div>
    </div>
    <script type="text/javascript">
        let regBtn = document.querySelector('.btn-register');
        let fieldPass1 = document.querySelector('#password');
        let fieldPass2 = document.querySelector('#confirm_password');
        regBtn.addEventListener('click', function () {
            if (fieldPass1.textContent !== fieldPass2.textContent)
                alert('Password mismatch');
        });
    </script>
@endsection
