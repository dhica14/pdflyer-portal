@extends('layouts.template')

@section('title', 'login')

@section('content')
<body>
    <div class="login text-center">
        <form class="form-login" method="post" action="/">
            @csrf
            <img src="images/logo.png" class="mb-4" width=100% height=100%>
            <h1 class="h3 mb-3 font-weight-normal">PORTAL</h1>
            <input type="text" id="id" name="id" class="form-control" placeholder="ID" required autofocus>
            <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
            <div class="checkbox mb-3">
            </div>
            <span style="color:red">
                {{ $message }}
            </span>
            <button id="login" class="btn btn-lg btn-primary btn-block" type="submit">Log in</button>
            <p class="mt-5 mb-3 text-muted">&copy; 2021 pdFlyer Development Team</p>
        </form>
    </div>
</body>
@endsection
