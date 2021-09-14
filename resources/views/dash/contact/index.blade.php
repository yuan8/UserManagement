@extends('adminlte::page')

@section('title', 'Contact')

@section('content_header')

<div class="row mb-2">
      <div class="col-sm-8">
        <h1>APP {{$app->name}}</h1>
            <span class=" badge badge-success "><b>{{route('api.message.store',['env'=>($app->wa_status==1?'production':'sanbox'),'uuid'=>$app->uuid])}}</b></span>
      </div><!-- /.col -->
     
    </div>

@stop
