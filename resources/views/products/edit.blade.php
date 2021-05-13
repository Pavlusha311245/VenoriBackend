@extends('layouts.app')
@section('title', 'Edit Product')
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
            <h1 style="text-align: center"> Edit product №{{$products->id}}</h1>
            <div class="form-group">
                {!! Form::open(['action' => ['App\Http\Controllers\ProductController@edit', $products->id], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                @csrf
                <div class="form-row">
                    {{Form::label('name','Name')}}
                    {{Form::text('name', $products->name ,['class' => 'form-control', 'placeholder' => 'Name'])}}
                </div>
                <div class="form-row">
                    {{Form::label('weight','Weight')}}
                    {{Form::text('weight', $products->weight ,['class' => 'form-control', 'placeholder' => 'Weight'])}}
                </div>
                <div class="form-row">
                    {{Form::label('price','Price')}}
                    {{Form::text('price', $products->price ,['class' => 'form-control', 'placeholder' => 'Price'])}}
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
                <div class="d-flex justify-content-between">
                    <a href="/admin/products/{{$products->id}}" class="btn btn-secondary">Go back</a>
                    {{Form::submit('Update',['class'=> 'btn btn-outline-primary'])}}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
