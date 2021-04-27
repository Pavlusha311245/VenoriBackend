@extends('layouts.app')
@section('title', 'Products')
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

        .products:not(:first-child):hover {
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
        @if(count($products)==0)
                <h2>There is no data to form the table</h2>
                <p style="text-align: center"><a href="/admin/products/create"><img src="https://img.icons8.com/nolan/64/plus.png" width="50" height="50"/></a></p>
        @else
            @if(session('message'))
                <div class="alert alert-success" style="margin-top: 20px">{{session('message')}}</div>
            @endif
            <table style="margin: 50px 0; min-width: 100%;">
                <tr style="background-color: rgba(122,117,226,0.5); text-align: center;">
                    <th style="width: 50px">Id</th>
                    <th>Name</th>
                    <th>Weight</th>
                    <th>Price</th>
                    <th>ImageUrl</th>
                    <th>CategoryId</th>
                    <th>Details</th>
                    <th>Remove</th>
                </tr>
                @foreach($products as $product)
                    <tr class="table-row">
                        <td style="text-align: center" id="products_id">{{$product['id']}}</td>
                        <td id="products_name">{{$product['name']}}</td>
                        <td id="products_weight">{{$product['weight']}}</td>
                        <td id="products_price">{{$product['price']}}</td>
                        <td id="products_image_url">{{$product['image_url']}}</td>
                        <td id="products_category_id">{{$product['category_id']}}</td>
                        <td>
                            <a href="/admin/products/{{$product->id}}" class="btn btn-outline-primary btn-sm">Show</a>
                        </td>
                        <td>
                            <a href="/admin/products/{{$product->id}}/delete" class="btn btn-danger btn-sm">Remove</a>
                        </td>
                    </tr>
                @endforeach
                <tr class="table-row">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: center"><a href="/admin/products/create"><img src="https://img.icons8.com/nolan/64/plus.png" width="30" height="30"/></a></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endif
            </table>
    </div>
</div>
@endsection
