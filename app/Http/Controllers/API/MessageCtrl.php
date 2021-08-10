<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use Storage;
use Carbon\Carbon;
class MessageCtrl extends Controller
{
    //

    public function store($env,$uuid,Request $request){

        $user=Auth::User();
        $app=DB::table('apps')->where([
            'user_id'=>$user->id,
            'uuid'=>$uuid 
        ])->first();
        
        if($app){
            $file_base_path='Production/user-'.$user->id.'/app-'.$app->id;
            $files_stored=[];
            if($request->files){
                // if(!is_array($request->files)){
                //     $request['files']=[$request->files];
                // }

                $valid=true;
                foreach($request->file('files')??[] as $key=>$file){
                    $name = time().'.'.$file->extension();
                    $path = $file->store(
                        $file_base_path.'/uploads', 'disk_app'
                    );
                    if($path){
                        $files_stored[]=explode($file_base_path,$path)[1];
                    }

                }

            }

            if(count($files_stored)){
                $use_file=true;
            }else{
                $use_file=false;

            }

           $a= DB::table('messages')->insertOrIgnore([
                'content_text'=>$request->content,
                'content_attach'=>($use_file?json_encode($files_stored):null),
                'message_type'=>($use_file?2:1),
                'to_number'=>$request->to,
                'app_id'=>$app->id,
                'created_at'=>Carbon::now()

            ]);

           return $a;




        }


    }
}
