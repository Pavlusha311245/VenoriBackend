@extends('layouts.app')
@section('title','Create Product')
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
            <h2>Create a new product</h2>
            {!! Form::open(['action' => ['App\Http\Controllers\ProductController@create'], 'method' => 'POST']) !!}
            @csrf
            <div class="form-row">
                {{Form::label('name','Name')}}
                {{Form::text('name','',['class' => 'form-control', 'placeholder' => 'Name'])}}
            </div>
            <div class="form-row">
                {{Form::label('weight','Weight')}}
                {{Form::text('weight','',['class' => 'form-control', 'placeholder' => 'Weight'])}}
            </div>
            <div class="form-row">
                {{Form::label('price','Price')}}
                {{Form::text('price','',['class' => 'form-control', 'placeholder' => 'Price'])}}
            </div>
            <div class="form-row">
                {{Form::label('image_url','ImageUrl')}}
                {{Form::text('image_url','',['class' => 'form-control', 'placeholder' => 'ImageUrl'])}}
            </div>
            <div class="form-row">
                {{Form::label('category_id','CategoryId')}}
                {{Form::text('category_id','', ['class' => 'form-control', 'placeholder' => 'CategoryId'])}}
            </div>

            {{Form::submit('Create',['class'=> 'btn btn-success btn-create'])}}
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

