<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
class AppCtrl extends Controller
{
    //

    public function index(){
        $app=DB::table('apps')->where('user_id',Auth::User()->id)->get();

        return view('dash.app.index')->with(['apps'=>$app]);

    }

    public function message($uuid,Request $request){
         $U=Auth::User();
        $app=DB::table('apps');
        if($U){
            $app=$app->where('user_id',$U->id);
        }
        $app=$app->where('uuid',$uuid)->first();

        if($app){
              $context=[
                    'app_section'=>$app->id,
                    'menu'=>'message'
                ];
                return view('dash.app.message')->with(['app'=>$app,'h_app_'=>$context]);

        }

    }

    public function detail($uuid){
        $U=Auth::User();
        $app=DB::table('apps');
        if($U){
            $app=$app->where('user_id',$U->id);
        }
        $app=$app->where('uuid',$uuid)->first();


        if($app){
             $context=[
                    'app_section'=>$app->id,
                    'menu'=>'dashboard'
                ];
             return view('dash.app.dashboard')->with(['app'=>$app,'h_app_'=>$context]);
        }


    }
}
