@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<div class="row mb-2">
      <div class="col-sm-8">
        <h1>APP {{$app->name}}</h1>
            <span class=" badge badge-success "><b>{{route('api.message.store',['env'=>($app->wa_status==1?'production':'sanbox'),'uuid'=>$app->uuid])}}</b></span>
      </div><!-- /.col -->
     
    </div>

    @include('component.nav_app')

@stop

@section('content')
    <div id="data-message">


    	<button class="btn btn-success " v-on:click="modal_add_show_form()" ><i class="fa fa-plus"></i> New Message</button>

    	<div class="card" style="margin-top:10px;">
    		<div class="card-header">
    			<h5>M</h5>
    		</div>
    		<div class="card-body">
    			<table class="table table-condensed table-hover">
    				<thead>
    					<tr>
    						<th>TO</th>
    						<th>STATUS</th>
    						<th>CONTENT</th>

    					</tr>
    				</thead>
    				<tbody>
    					<tr v-for="item in table.data">
    						<td>@{{item.to}}</td>
    						<td>@{{table.status_text(item.status)}}</td>
    						<td>@{{item.content}}</td>

    					</tr>
    				</tbody>
    			</table>
    		</div>
    	</div>

  
    	<div class="modal fade" id="modal-add" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	      <div class="modal-dialog modal-dialog-scrollable|modal-dialog-centered modal-sm|modal-lg|modal-xl" role="document">
	        <div class="modal-content">
	          <div class="modal-header">
	            <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
	            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	              <span aria-hidden="true">&times;</span>
	            </button>
	          </div>
	          <div class="modal-body">
	            ...
	          </div>
	          <div class="modal-footer">
	            <button type="button" class="btn btn-primary|secondary|success|danger|warning|info|light|dark" data-dismiss="modal">Close</button>
	            <button type="button" class="btn btn-primary|secondary|success|danger|warning|info|light|dark">Save changes</button>
	          </div>
	        </div>
	      </div>
	    </div>

	    <div class="row">
	    	<div class="col-12">
	    		<table class="table-bordered"></table>
	    	</div>
	    </div>
    </div>
@stop

@section('js')
<script type="text/javascript">
	var data_message=new Vue({
		el:"#data-message",
		data:{
			modal_add:{

				content_text:null,
				files:[]
			},
			table:{
				query:{

				},
				data:[
				{
					to:"08777192898298",
					content:"dsdsds"
				},
				
				],
				status_text:function(status){
					return 'ok';
				}
			}
		},methods:{
			modal_add_show_form:function(){
				$('#modal-add').modal();
			}

		}
	})
</script>

@stop