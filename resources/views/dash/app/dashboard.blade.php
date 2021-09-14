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
      <div class="col-lg-3 col-6" v-if="rekap.mess">
        <!-- small box -->
        <div class="small-box bg-info" >
          <div class="inner">
            <h3>@{{rekap.mess.value}}</h3>

            <p>@{{rekap.mess.unit}}</p>
          </div>
          <div class="icon">
            <i class="ion ion-bag"></i>
          </div>
          <a v-bind:href="rekap.mess.url" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
      <div class="col-lg-3 col-6" v-if="rekap.queue">
        <!-- small box -->
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3>@{{rekap.queue.value}} <span style="font-size:10px;">Success</span></h3>

            <p>@{{rekap.queue.unit}}</p>
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
            <img onerror="imgError()" style="max-height: 50px; border-radius:50%; border: 1px solid #d0d0d0;" v-bind:src="(status.wa_status=='READY')?(urlhost+'/wa-file/'+status.wa_number+'/pp-'+status.wa_number+'.jpg?v='+(new Date())):avaerror" >
            @{{(status.wa_status?(status.wa_status=='READY'?status.wa_status+' - '+status.wa_number:status.wa_status):'EGINE NOT RUNING')}} 
        </div>
      <div class="card-body">
        <div class="row row-eq-height no-gutters" v-if="status.wa_status=='LOGIN QR' && status.wa_token" >
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

   </div>
@stop

@section('css')
@stop

@section('js')

<script type="text/javascript">
  function imgError(){
    var old_src=this.src;
    setTimeout(()=>{
      this.src='{{url('images/loader.gif')}}';
    },500);
    setTimeout(()=>{
      this.src=old_src;
    },3000);
  }

  var urlhost='{{url('dashboard/app/'.$app->uuid)}}';
    var rekap=new Vue({
      'el':'#app_rekap',
      'data':{
        rekap:[]
      },
      methods:{
        init:function(){
          setInterval(function(){
          AXPOST('{{route('api.message.rekap',['env'=>'production','uuid'=>$app->uuid])}}',{}).then(function(res){
              window.rekap.rekap=res.data;
            });
          },2000);
        }
      }
    }); 

    rekap.init();

    var dash_app_status=new Vue({
      'el':'#app_status',
      data:{
        urlhost:urlhost,
        color:"green",
        avaerror:'{{url('images/loader.gif')}}',
        status:{
          "app":false,
          "x":0,
          "wa_number":"",
          "wa_status":"WAITING CONNECTION",
          "wa_token":null,
          "wa_pid":null,
          "color":""
        },
      },
      components: {VueQr},
      computed:{
        status_text:function(){
          this.status.wa_status;
        }
      },
      watch:{
        "status.x":function(val,old) {
          switch(this.status.wa_status){
            case 'D':
              this.status.color='';
            break;
             case 'INITAL':
              this.status.color='bg-warning';
            break;
            case 'S':
              this.status.color='bg-primary';
            break;
            case 'READY':
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
          this.status.wa_status='RESTART SERVER';
          this.status.wa_token=null;
          AXPOST('{{route('api.egine.start',['env'=>'production','uuid'=>$app->uuid])}}',{}).then(function(res){
              console.log('res',res);
          });
        },
        res_token:function(){
          this.status.wa_status='DELETE TOKEN AKSES';
          this.status.wa_token=null;

          AXPOST('{{route('api.egine.new-token',['env'=>'production','uuid'=>$app->uuid])}}',{}).then(function(res){
              console.log('res',res);
          });
        },
        init:function(){
          setInterval(function(){
            AXPOST('{{route('api.egine.status',['env'=>'production','uuid'=>$app->uuid])}}',{}).then(function(res){
              // console.log(res);

            if(res){
                window.dash_app_status.status.wa_status=res.data.status_client;
                window.dash_app_status.status.wa_token=res.data.qr_login;
                window.dash_app_status.status.wa_number=res.data.user.user;
                window.dash_app_status.status.x=Math.random();
            }

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