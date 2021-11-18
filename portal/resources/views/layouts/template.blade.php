<!doctype html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <title>@yield('title')</title>
        <link rel='stylesheet' href='{{ asset('css/app.css') }}' />
        <link rel='stylesheet' href='{{ asset('css/style.css') }}' />
        <link rel="stylesheet" href='{{ asset('bootstrap/dist/css/bootstrap.min.css')}}'>
    </head>
    <body>
        @yield('content')
        <script src='{{ asset('bootstrap/dist/js/bootstrap.min.js')}}'></script>
    </body>
</html>
