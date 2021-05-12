@extends('layouts.app')
@section('title','Create User')
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
            <h2>Create new user</h2>
            {!! Form::open(['action' => ['App\Http\Controllers\UserController@create'], 'method' => 'POST']) !!}
            @csrf
            <div class="form-row">
                {{Form::label('first_name','Name')}}
                {{Form::text('first_name', '' ,['class' => 'form-control', 'placeholder' => 'Name'])}}
            </div>
            <div class="form-row">
                {{Form::label('second_name','Surname')}}
                {{Form::text('second_name','',['class' => 'form-control', 'placeholder' => 'Surname'])}}
            </div>
            <div class="form-row">
                {{Form::label('email','Email')}}
                {{Form::text('email', '' ,['class' => 'form-control', 'placeholder' => 'Email'])}}
            </div>
            <div class="form-row">
                {{Form::label('password','Password')}}
                {{Form::password('password',['class' => 'form-control', 'placeholder' => 'Password'])}}
            </div>
            <div class="form-row">
                {{Form::label('confirm_password','Confirm Password')}}
                {{Form::password('confirm_password',['class' => 'form-control', 'placeholder' => 'Repeat password'])}}
            </div>
            <div class="form-row">
                {{Form::label('rolePicker', 'Role')}}
                <select class="form-select" id="rolePicker" name="role">
                    @foreach($roles as $role)
                        <option value="{{$role->name}}">{{$role->name}}</option>
                    @endforeach
                </select>
            </div>
            {{Form::submit('Create',['class'=> 'btn btn-success btn-register'])}}
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
