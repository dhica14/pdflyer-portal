@extends('layouts.template')

@section('title', 'Project')

@section('content')

@include('layouts/header')
<div class="container-fluid">
    <div class="row">
        @include('layouts/sidebar')
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Project</h1>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">sl_id</th>
                            <th scope="col">Last updated</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $cnt = $smartlink_count ?>
                        @for ($i = 0; $i < $cnt; $i++)
                        {{-- <form action="{{ route('projectSelect') }}" method="POST"> --}}
                        <form action='project/select' method="POST">
                            @csrf
                            <tr>
                                <td scope="col" class="text-wrap">
                                    {{ $i + 1 }}
                                </td>
                                <td scope="col" class="text-wrap">
                                    {{ $smartlink_lists[$i]['title'] }}
                                    <input type="hidden" name="title" value="{{ $smartlink_lists[$i]['title'] }}">
                                </td>
                                <td scope="col" class="text-wrap">
                                    {{ $smartlink_lists[$i]['id'] }}
                                    <input type="hidden" name="id" value="{{ $smartlink_lists[$i]['id'] }}">
                                </td>
                                <td scope="col" class="text-wrap">
                                    {{ $smartlink_lists[$i]['updated_at'] }}
                                </td>
                                <td scope="col-md-2" style="white-space: normal;">
                                    <input type="submit" class="btn btn-sm btn-outline-info" value="Select" />
                                </td>
                            </tr>
                        </form>
                        @endfor
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
@endsection
