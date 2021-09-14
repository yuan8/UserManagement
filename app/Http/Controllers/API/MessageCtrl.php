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

    public function rekap($env,$uuid,Request $request){
        $user=Auth::User();
        $app=DB::table('apps')->where([
            'user_id'=>$user->id,
            'uuid'=>$uuid 
        ])->first();
        if($app){
            $return=[];

            $count=DB::table('messages as m')
            ->where('app_id',$app->id)
            ->selectRaw(
                "count(case when m.status=1 then m.id else null end) as status_sended,
                count(case when m.status=0 then m.id else null end) as status_queue"
            )->first();

            if($count){
                $return['mess']=[
                        'unit'=>'Messages',
                        'value'=>($count->status_sended??0)+($count->status_queue??0),
                        'url'=>''
                ];

                $return['queue']=[
                        'unit'=>'Messages Queue',
                        'value'=>($count->status_queue??0).' / '.($count->status_sended??0),
                        'url'=>''
                ];

               

                
               
            }

             return array(
                    'code'=>200,
                    'data'=>$return,
                    'type'=>'html',
                    'message'=>null
                );
        }

        return array(
            'code'=>200,
            'data'=>[],
            'type'=>'html',
            'message'=>null
        );
    }

    public function store($env,$uuid,Request $request){

        $user=Auth::User();
        $app=DB::table('apps')->where([
            'user_id'=>$user->id,
            'uuid'=>$uuid 
        ])->first();
        $res_count=0;

        if($app){
           if($request->bulk){
            foreach($request->bulk as $p){
                $p=(Object)$p;
                $file_base_path='Production/user-'.$user->id.'/app-'.$app->id;
                $files_stored=[];
                if(isset($p->files)){
                    $valid=true;
                    foreach($p->file('files')??[] as $key=>$file){
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
                    'content_text'=>$p->content,
                    'content_attach'=>($use_file?json_encode($files_stored):null),
                    'message_type'=>($use_file?2:1),
                    'to_number'=>$p->to,
                    'app_id'=>$app->id,
                     'send_date'=>(!isset($p->send_date))?Carbon::parse($p->send_date):Carbon::now(),
                    'created_at'=>Carbon::now()

                ]);

               if($a){
                $res_count+=1;
               }

            }



           }else{
             $file_base_path='Production/user-'.$user->id.'/app-'.$app->id;
            $files_stored=[];
            if($request->files){
               

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
                'send_date'=>(!isset($request->send_date))?Carbon::parse($request->send_date):Carbon::now(),
                'app_id'=>$app->id,
                'created_at'=>Carbon::now()

            ]);

           return $a;
           }






        }

            return $res_count;
        


    }
}
