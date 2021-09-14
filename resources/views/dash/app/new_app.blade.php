@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    

<div class="row mb-2">
      <div class="col-sm-8">
        <h1>New App</h1>
            <span class=" badge badge-success "></span>
      </div>
     
</div>
@stop

@section('content')

<div class="card">
        <form method="post" action="{{route('dash.app.store')}}">

    <div class="card-body">
            @csrf
           <div class="form-group">
             <label>Name App *</label>
                <input type="text" class="form-control" name="name" required="">
            </div>
             <div class="form-group">
                <label>Host Access API (http:// , https://)</label>
                <input type="text" class="form-control" name="host" >
            </div>
            <div class="form-group">
                <label>Url Received Report (Method POST)</label>
                <input type="text" class="form-control" name="url_received" >
            </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-success">Send</button>
    </div>
        </form>
    
</div>

@stop