@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>APP</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table-bordered table">
                    <thead>
                        <tr>
                           <th>#</th>
                           <th>NAME</th>
                           <th>NUMBER</th>
                           <th>PURCASE STATUS</th>

                           <th>STATUS</th>


                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($apps as $app)
                            @php
                                // dd($app);

                            @endphp
                            <tr>
                                <td>{{$app->uuid}}</td>
                                <td>{{$app->name}}</td>
                                <td>{{$app->wa_number}}</td>
                                <td>{{$app->status_active}}</td>
                                <td>{{$app->wa_status}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> console.log('Hi!'); </script>
@stop