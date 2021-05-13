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
            {!! Form::open(['action' => ['App\Http\Controllers\ProductController@create', $place_id], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
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
                {{Form::label('image','Image')}}
                {{Form::file('image',['class' => 'form-control', 'name' => 'image', 'placeholder' => 'Image'])}}
            </div>
            <div class="form-row">
                {{Form::label('categoryPicker', 'Category')}}
                <select class="form-select" name="category_id" id="categoryPicker">
                    @foreach($categories as $category)
                        <option value="{{$category->id}}">{{$category->name}}</option>
                    @endforeach
                </select>
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

