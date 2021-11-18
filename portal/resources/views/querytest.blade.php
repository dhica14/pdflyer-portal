@extends('layouts.template')

@section('title', 'QueryTest')

@section('content')

@include('layouts/header')

<div class="container-fluid">
    <div class="row">
        @include('layouts/sidebar')
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-1 border-bottom">
                <h1 class="h2">QueryTest</h1>
            </div>
            <div class="d-grid gap-3">
                <form method="post" name="form01" action="execute">
                    @csrf
                    <h3>query</h3>
                    <div class="d-grid gap-3">
                        <div class="col-md-6">
                            <label for="inputsSmartlink" class="form-label" style="color: red;">※Smartlink's ID</label>
                            <input type="text" class="form-control" name="sl" id="inputsSmartlink" value="{{ $sl_id }}">
                        </div>
                        <div class="col-md-3">
                            <label for="inputFrom" class="form-label">From</label>
                            <input type="date" class="form-control" name="from" id="inputFrom" value="{{ $from }}">
                        </div>
                        <div class="col-md-3">
                            <label for="inputTo" class="form-label">To</label>
                            <input type="date" class="form-control" name="to" id="inputTo" value="{{ $to }}">
                        </div>
                        <div>
                            <input id="get-report" type="submit" class="btn btn-sm btn-primary" value="Execute" />
                        </div>
                    </div>
                </form>
            </div>
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-1 border-bottom"></div>
            <h4>status code : {{ $code }}
            </h4>
            <h4>message</h4>
            <span style="color: red;">
                <h4>{{ $message }}</h4>
            </span>
            <div class="card overflow-auto mb-1"
                style="width: 100%; height: 70%; background-color: black;color: white;">
                {{ $result }}
            </div>
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-1 border-bottom"></div>
        </main>
    </div>
</div>
@endsection
