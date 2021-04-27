@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
    <style>
        .role {
            background-color: #7cffb4;
            padding: 5px;
            border-radius: 5px;
            transition: all .5s;
            cursor: pointer;
        }

        .role:hover {
            background-color: red;
            color: white;
        }

        .addRole {
            display: inline;
        }

        .hide {
            display: none;
        }

        .hasRole {
            display: inline;
        }
    </style>
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
                    <h3>Roles</h3>
                    <div class="roles">
                        @foreach($user->roles as $role)
                            <div class="hasRole">
                                <label class="role" for="role{{$role->name}}">{{$role->name}}</label>
                                <input type="hidden" value="{{$role->name}}" name="role[]" id="role{{$role->name}}">
                            </div>
                        @endforeach
                        <button type="button" class="btn btn-primary" id="addRoleBtn">+</button>
                        <div class="addRole hide">
                            <select>
                                @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                    @if(!$user->hasRole($role->name))
                                        <option value="{{$role->name}}">{{$role->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-success" id="createRoleBtn">✓</button>
                            <button type="button" class="btn btn-danger" id="cancelCreationRoleBtn">✘</button>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <a href="/admin/users/{{$user->id}}" class="btn btn-secondary">Go back</a>
                    {{Form::submit('Update',['class'=> 'btn btn-outline-primary'])}}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <script type="text/javascript">
        let roles = document.querySelectorAll('.role');
        roles.forEach(role => {
            role.addEventListener('click', function () {
                let inputRole = document.querySelector('#' + role.getAttribute('for'));
                inputRole.remove();
                role.remove();
            })
        });

        let addRoleBlock = document.querySelector('.addRole');
        let rolesBlock = document.querySelector('.roles');

        let addRoleBtn = document.querySelector('#addRoleBtn');
        let cancelCreationRoleBtn = document.querySelector('#cancelCreationRoleBtn');

        function ToggleCreationRole() {
            addRoleBtn.classList.toggle('hide');
            addRoleBlock.classList.toggle('hide');
        }

        addRoleBtn.addEventListener('click', () => {
            ToggleCreationRole();
        });

        cancelCreationRoleBtn.addEventListener('click', () => {
            ToggleCreationRole();
        });

        let createRoleBtn = document.querySelector('#createRoleBtn');
        createRoleBtn.addEventListener('click', function () {
            ToggleCreationRole();
            let selectedRole = document.querySelector('select').value;
            let block = document.createElement('div');
            block.classList.add('hasRole');
            block.innerHTML = "<label class='role' for='role'>" + selectedRole + "</label>" +
                "<input type='hidden' value='" + selectedRole + "' name='role[]' id='role" + selectedRole + "'>";
            block.addEventListener('click', () => {
                block.remove();
            })
            addRoleBtn.before(block);
        });


    </script>
@endsection
