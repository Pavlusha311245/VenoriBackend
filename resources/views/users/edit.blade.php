@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
    <div class="d-flex justify-content-center">
        <div class="whiteBlockPurpleBorder">
            <h1 style="text-align: center"> Edit user №{{$user->id}}</h1>
            <div class="form-group">
                {!! Form::open(['action' => ['App\Http\Controllers\UserController@edit', $user->id], 'method' => 'POST']) !!}
                @csrf
                <div class="form-row">
                    {{Form::label('first_name','Name')}}
                    {{Form::text('first_name', $user->first_name ,['class' => 'form-control', 'placeholder' => 'Name'])}}
                </div>
                <div class="form-row">
                    {{Form::label('second_name','Surname')}}
                    {{Form::text('second_name', $user->second_name ,['class' => 'form-control', 'placeholder' => 'Surname'])}}
                </div>
                <div class="form-row">
                    {{Form::label('email','Email')}}
                    {{Form::text('email', $user->email ,['class' => 'form-control', 'placeholder' => 'Email'])}}
                </div>
                <div class="form-row">
                    {{Form::label('role','Roles')}}
                    @foreach($user->roles as $role)
                        <span style="background-color: #7cffb4; padding: 5px; border-radius: 5px">{{$role['name']}}</span>
                    @endforeach
                    <select name="role" id="role">
                        @foreach(\Spatie\Permission\Models\Role::all() as $role)
                            <option value="{{$role->name}}">{{$role->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="/admin/users/{{$user->id}}" class="btn btn-secondary">Go back</a>
                    {{Form::submit('Update',['class'=> 'btn btn-outline-primary'])}}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
