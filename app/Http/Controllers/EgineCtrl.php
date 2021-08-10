<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use R;
use Illuminate\Support\Facades\Artisan;
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
            $app=DB::table('apps');
            if($U){
                $app=$app->where('user_id',$U->id);
            }
            $app=$app->where('uuid',$uuid)->first();
            if($app){
                if(file_exists(app_path('WaBot/Production/user-'.$app->user_id.'/app-'.$app->id.'/app.session.json'))){
                    unlink(app_path('WaBot/Production/user-'.$app->user_id.'/app-'.$app->id.'/app.session.json'));
                     if(file_exists(app_path('WaBot/Production/user-'.$app->user_id.'/app-'.$app->id.'/app.setting.json'))){
                       $setting=json_decode(app_path('WaBot/Production/user-'.$app->user_id.'/app-'.$app->id.'/app.setting.json'),true);
                       $setting['pid']=null;
                       $setting['token']=null;
                       $setting['status']=0;
                       file_put_contents(app_path('WaBot/Production/user-'.$app->user_id.'/app-'.$app->id.'/app.setting.json'),json_encode($setting));

                    }
                }

                dd(Artisan::call('wa:run',['id'=>$app->id]));


                return R::response(['data'=>$app,'message'=>'success'],200);

            }
        }
    }

    public function start($env,$uuid){
        $U=Auth::User();
        
        if($U){
            $app=DB::table('apps');
            
            if($U){
                $app=$app->where('user_id',$U->id);
            }

            $app=$app->where('uuid',$uuid)->first();
            
            if($app){
                Artisan::call('wa:run',['id'=>$app->id]);
                return R::response(['data'=>$app,'message'=>'success'],200);
            }
        }
    }


}
