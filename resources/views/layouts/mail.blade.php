<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'Laravel') }}</title>
</head>
<body>
<style>
    .main-block {
        margin: 20px;
        border-radius: 10px;
        border: 1px solid #718096;
        background-color: #718096;
        padding: 20px;
    }
</style>
@yield('content')
</body>
</html>
