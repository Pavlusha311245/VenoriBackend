<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <title>@yield('title')</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark bg-gradient">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">VENORI</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">

                @if(\Illuminate\Support\Facades\Auth::check())
                    @if(\Illuminate\Support\Facades\Auth::user()->hasRole('Admin'))
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/dashboard">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/users">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/managersConfirmation">Managers confirmation</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/products">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/places">Places</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/orders">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/user/resetPassword">Reset password</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">Logout</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Log In to Admin Panel</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
<style>
    body {
        background: rgb(34, 213, 245);
        background: linear-gradient(90deg, rgba(34, 213, 245, 1) 0%, rgba(255, 171, 248, 1) 27%, rgba(229, 178, 233, 1) 51%, rgba(242, 207, 252, 1) 73%, rgba(254, 111, 248, 1) 100%);
    }

    .form-row {
        margin-bottom: 15px;
    }

    .form-row label {
        margin-bottom: 7px;
    }

    .whiteBlockPurpleBorder {
        width: 500px;
        padding: 15px;
        margin: 100px 0;
        border-radius: 5px;
        border: 1px solid #9561e2;
        background-color: rgba(255, 255, 255, 0.5);
        height: min-content;
    }
</style>

@yield('content')

<script type="text/javascript">
    window.addEventListener('load', () => {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(function () {
                alert.remove();
            }, 5000);
        });
    });





</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-lpyLfhYuitXl2zRZ5Bn2fqnhNAKOAaM/0Kr9laMspuaMiZfGmfwRNFh8HlMy49eQ"
        crossorigin="anonymous"></script>
</body>
</html>
