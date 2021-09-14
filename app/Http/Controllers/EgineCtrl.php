<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use R;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
class EgineCtrl extends Controller
{
    //


    public function status($env,$uuid){
        $U=Auth::User();
        if($U){
            $app=DB::table('apps');
            if($U){
                $app=$app->where('user_id',$U->id);
            }
            $app=$app->where('uuid',$uuid)->first();
            if($app){

                $data=json_decode(file_get_contents(app_path('WaBot/Production/user-'.$app->user_id.'/app-'.$app->id.'/app.status.json')));
                return R::response(['data'=>$data,'message'=>'success'],200);
            }
        }
    }


    public function newToken($env,$uuid){
         $U=Auth::User();
        if($U){
            $app=DB::table('apps')->where('user_id',$U->id)->where('uuid',$uuid)->first();
            if($app){
                static::start($env,$uui);
                if(file_exists(app_path('WaBot/Production/user-'.$app->user_id.'/app-'.$app->id.'/app.session.json'))){
                    unlink(app_path('WaBot/Production/user-'.$app->user_id.'/app-'.$app->id.'/app.session.json'));
                     if(file_exists(app_path('WaBot/Production/user-'.$app->user_id.'/app-'.$app->id.'/app.setting.json'))){
                       $setting=json_decode(app_path('WaBot/Production/user-'.$app->user_id.'/app-'.$app->id.'/app.setting.json'),true);
                       $setting['wa_status']=null;
                       $setting['token']=null;
                       file_put_contents(app_path('WaBot/Production/user-'.$app->user_id.'/app-'.$app->id.'/app.setting.json'),json_encode($setting));

                    }
                }



                return R::response(['data'=>$app,'message'=>'success'],200);

            }
        }

        return R::response(['data'=>[],'message'=>'error'],500);

    }

    

    public static  function start($env,$uuid){
        $U=Auth::User();
        
        if($U){
            $app=DB::table('apps');
            
            if($U){
                $app=$app->where('user_id',$U->id);
            }

            $app=$app->where('uuid',$uuid)->first();
            
            if($app){
                
                if(file_exists(app_path('WaBot/Production/user-'.$U->id.'/app-'.$app->id.'/app.js'))){
                        $perintah=[
                            'method'=>'start',
                            'app'=>[
                                'script'=>app_path('WaBot/Production/user-'.$U->id.'/app-'.$app->id.'/app.js'),
                                'name'=>'wabot-app-'.$app->id
                            ]

                        ];

                        $app_status = Redis::publish('server',json_encode($perintah,true));
                        return R::response(['data'=>$app,'message'=>'success'],200);



                }else{
                        return R::response(['data'=>[],'message'=>'error'],500);
                    
                }
            }
        }

        return R::response(['data'=>[],'message'=>'error'],500);

    }


}
