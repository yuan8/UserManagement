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
   
   <div class="row" id="app_rekap">
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-info" v-if="rekap.mall">
          <div class="inner">
            <h3>@{{rekap.mall.value}}</h3>

            <p>@{{rekap.mall.unit}}</p>
          </div>
          <div class="icon">
            <i class="ion ion-bag"></i>
          </div>
          <a v-bind:href="rekap.mall.url" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
          <div class="inner">
            <h3>53<sup style="font-size: 20px">%</sup></h3>

            <p>Message Qeue</p>
          </div>
          <div class="icon">
            <i class="ion ion-stats-bars"></i>
          </div>
          <a href="#" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
          <div class="inner">
            <h3>44</h3>

            <p>Contact</p>
          </div>
          <div class="icon">
            <i class="ion ion-person-add"></i>
          </div>
          <a href="#" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box bg-teal">
          <div class="inner">
            <h3>65</h3>

            <p>Group</p>
          </div>
          <div class="icon">
            <i class="ion ion-pie-graph"></i>
          </div>
          <a href="#" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
    </div>
    <h4><b>Egine Status </b></h4>
    <div class="card" id="app_status">
        <div v-bind:class="'card-header '+(status.color) ">
            @{{status_text}} 
        </div>
      <div class="card-body">
        <div class="row row-eq-height no-gutters" v-if="status.wa_status==2 && status.wa_token" >
          <div class="col-6 text-center" >
            <div v-bind:style="'-webkit-transition: background-color 1000ms linear; -ms-transition: background-color 1000ms linear;transition: background-color 1000ms linear; '+'background-color:'+color+';'+'padding-top:10px; padding-bottom:10px; height:100%'  ">
            <vue-qr  v-bind:text="status.wa_token" qid="{{$app->uuid}}"></vue-qr>
              
            </div>
           
        </div>
          <div class="col-6 text-center">
            <img src="{{asset('dist/images/sc-wa.png')}}" style="max-width: 100%; height:100%;">
          </div>
        </div>
        
      </div>
      <div class="card-footer">
        <div class="btn-group">
            <button @click='start_egine' class="btn btn-outline-danger">Restart</button>
            <button @click="res_token" class="btn btn-outline-info">Get New Token</button>

        </div>
      </div>
    </div>
   <div class="card text-dark bg-light vue-com" >
     <div class="card-header">
        <h5><b>Message Queue</b></h5>
     </div>
     <div class="card-body">
      
     </div>
     <div class="card-footer text-muted">
     </div>
   </div>
@stop

@section('css')
@stop

@section('js')

<script type="text/javascript">
    var rekap=new Vue({
      'el':'#app_rekap',
      'data':{
        rekap:[]
      },
      methods:{
        init:function(){
          AXGET('{{route('api.message.rekap',['env'=>'production','uuid'=>$app->uuid])}}',{}).then(function(res){
              console.log('res',res);
          });
        }
      }
    }); 

    rekap.init();

    var dash_app_status=new Vue({
      'el':'#app_status',
      data:{
        color:"green",
        status:{
          "app":false,
          "x":0,
          "wa_number":null,
          "wa_status":null,
          "wa_token":null,
          "wa_pid":null,
          "color":""
        },
      },
      components: {VueQr},
      computed:{
        status_text:function(){
          switch(parseInt(this.status.wa_status)){
            case 0:
              return 'Egine Stack try to restart';
            break;
            case 99:
              return 'Restarting Server';
            break;
             case 1:
              return 'App Start';
            break;
            case 2:
              return 'Authorization Form';
            break;
            case 5:
              return 'Whatsapp Ready - '+this.status.wa_number;
            break;
            default:
            return '-';
            break;

          }
        }
      },
      watch:{
        "status.x":function(val,old) {
          switch(this.status.wa_status){
            case 0:
              this.status.color='';
            break;
             case 1:
              this.status.color='bg-warning';
            break;
            case 2:
              this.status.color='bg-primary';
            break;
            case 5:
              this.status.color='bg-green';
            break;
            default:
              this.status.color='';
            return '-';
            break;

          }
          
          
        },
        "status.wa_token":function(val,old){
            if(val!=old){
              this.color='#'+Math.floor(Math.random()*16777215).toString(16);
            }
          }
      },
      methods:{
        start_egine:function(){
          this.status.wa_status=1;
          this.status.wa_token=null;

          AXPOST('{{route('api.egine.start',['env'=>'production','uuid'=>$app->uuid])}}',{}).then(function(res){
              console.log('res',res);
          });
        },
        res_token:function(){
          this.status.wa_status=99;
          this.status.wa_token=null;

          AXPOST('{{route('api.egine.new-token',['env'=>'production','uuid'=>$app->uuid])}}',{}).then(function(res){
              console.log('res',res);
          });
        },
        init:function(){
          setInterval(function(){
            AXPOST('{{route('api.egine.status',['env'=>'production','uuid'=>$app->uuid])}}',{}).then(function(res){
              window.dash_app_status.status.wa_status=res.data.wa_status;
              window.dash_app_status.status.wa_token=res.data.wa_token;
              window.dash_app_status.status.wa_number=res.data.wa_number;
              window.dash_app_status.status.x=Math.random();

            });
          },3000);

        }
      }
    });

    $(()=>{
      dash_app_status.init();
    });

</script>
@stop