@extends('layouts.app')
@section('title', 'Users')
@section('content')
    <style>
        table {
            border-collapse: collapse;
            border: 1px solid #9561e2;
        }

        select {
            margin: 0;
        }

        th, td {
            padding: 5px;
        }

        .table-row:not(:first-child) {
            transition: all 0.5s;
            padding: 1px;
            background-color: rgba(255, 255, 255, 0.5);
        }

        .users:not(:first-child):hover {
            background-color: rgba(149, 97, 226, 0.5);
        }

        .update_form {
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #9561e2;
            background-color: rgba(255, 255, 255, 0.5);
        }
    </style>

    <div class="d-flex justify-content-center" style="height: 750px">

        <div style="height: min-content">
            @if(count($users)==0)
                <h2>There is no data to form the table</h2>
            @else
                @if(session('message'))
                    <div class="alert alert-success" style="margin-top: 20px">{{session('message')}}</div>
                @endif
                <table style="margin: 50px 0; min-width: 100%;">
                    <tr style="background-color: rgba(122,117,226,0.5); text-align: center;">
                        <th style="width: 50px">Id</th>
                        <th>Name</th>
                        <th>Surname</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Details</th>
                        <th>Remove</th>
                    </tr>
                    @foreach($users as $user)
                        <tr class="table-row">
                            <td style="text-align: center" id="user_id">{{$user['id']}}</td>
                            <td id="user_name">{{$user['first_name']}}</td>
                            <td id="user_surname">{{$user['second_name']}}</td>
                            <td id="user_email">{{$user['email']}}</td>
                            <td>
                                <div class="d-flex flex-wrap">
                                    @foreach($user->roles as $role)
                                        <span
                                            style="background-color: #7cffb4; padding: 5px; border-radius: 5px">{{$role['name']}}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <a href="/admin/users/{{$user->id}}" class="btn btn-outline-primary btn-sm">Show</a>
                            </td>
                            <td>
                                <a href="/admin/users/{{$user->id}}/delete" class="btn btn-danger btn-sm">Remove</a>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="table-row">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: center"><a href="/admin/users/create"><img
                                    src="https://img.icons8.com/nolan/64/plus.png" width="30" height="30"/></a></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endif
                </table>

        </div>
    </div>
@endsection
