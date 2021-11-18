@extends('layouts.template')

@section('title', 'Statistics')

@section('content')

@include('layouts/header')
<div class="container-fluid">
    <div class="row">
        @include('layouts/sidebar')
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-1 border-bottom">
                <h1 class="h2">Project > {{ $projename }}</h1>
            </div>
            <div class="d-grid gap-3">
                <form method="post" name="form01" action="export">
                    @csrf
                    <input type="hidden" name="title" value="{{ $projename }}" />
                    <h3>query</h3>
                    <div class="d-grid gap-3">
                        <div class="col-md-6">
                            <label for="inputsSmartlink" class="form-label" style="color: red;">※Smartlink's ID</label>
                            <input type="text" class="form-control" name="sl" id="inputsSmartlink" value="{{ $sl_id }}" readonly>
                            <input type="hidden" name="sl" value="{{ $sl_id }}" />
                        </div>
                        <div class="col-md-3">
                            <label for="inputFrom" class="form-label">From</label>
                            <input type="date" class="form-control" name="from" id="inputFrom">
                        </div>
                        <div class="col-md-3">
                            <label for="inputTo" class="form-label">To</label>
                            <input type="date" class="form-control" name="to" id="inputTo">
                        </div>
                        <div>
                            <input id="dl-report" type="submit" class="btn btn-sm btn-success" value="Export To CSV" />
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
@endsection
