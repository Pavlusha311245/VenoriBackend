<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style>
    .detail {
        color: #2d3748;
    }
</style>
<body>
<div>
    <h1 style="text-align: center; color: #38c172">Your order is accepted.</h1>
    <div>
        <h2>Order details:</h2>
        <p class="detail">Price: {{$order->price}}</p>
        <p class="detail">People: {{$order->people}}</p>
        <p class="detail">Date: {{$order->date}}</p>
        <p class="detail">Time: {{$order->time}} - {{$order->staying_end}}</p>
    </div>
</div>
</body>
</html>
