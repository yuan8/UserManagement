<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;
class AppCtrl extends Controller
{
    //



    static function  readfile_chunked($filename, $retbytes = TRUE) {
    $buffer = '';
    $cnt    = 0;
    $handle = fopen($filename, 'rb');

    if ($handle === false) {
        return false;
    }

    while (!feof($handle)) {
        $buffer = fread($handle, CHUNK_SIZE);
        echo $buffer;
        ob_flush();
        flush();

        if ($retbytes) {
            $cnt += strlen($buffer);
        }
    }

    $status = fclose($handle);

    if ($retbytes && $status) {
        return $cnt; // return num. bytes delivered like readfile() does.
    }

    return $status;

}

    public function get_file($uuid,$number,$file_name){
        define('CHUNK_SIZE', 1024*1024);
        $app=DB::table('apps')->where('uuid',$uuid)->first();
        if($app){
            // dd(app_path('WaBot/'.$app->path_app.'downloads/'.$file_name));
            if(file_exists(app_path('WaBot/'.$app->path_app.'downloads/'.$file_name))){
                $file=app_path('WaBot/'.$app->path_app.'downloads/'.$file_name);
                $mime=mime_content_type($file);
                 header("Content-Type:".$mime);
                  static::readfile_chunked($file);
            }else{
                return null;
            }
        }
        return null;


    }
    public function index(){
        $app=DB::table('apps')->where('user_id',Auth::User()->id)->get();
        return view('dash.app.index')->with(['apps'=>$app]);

    }

        public function new(){
             $U=Auth::User();
             return view('dash.app.new_app');
        }

        static function token($uid,$uuid){
            return Uuid::generate(3,$uuid.'_'.$uuid.'_'.date('is'), Uuid::NS_DNS);
        }

      public function store(Request $request){

         $U=Auth::User();
         $exist=DB::table('apps')->where('user_id',$U->id)->where('name','like',$request->name)->first();
         if($exist){

         }else{
            $uuid=Uuid::generate(3,$U->id.'_'.$request->name, Uuid::NS_DNS);

            $app_id=DB::table('apps')->insertGetId([
                "uuid"=>$uuid,
                "name"=>$request->name,
                "path_app"=>'Production/user-'.$U->id.'/',
                "host_attemp"=>$request->host,
                "host_receive"=>$request->url_received,
                "token_access"=>static::token($U->id,$uuid),
                "status_active"=>0,
                "user_id"=>$U->id,
                "created_at"=>Carbon::now(),
                "updated_at"=>Carbon::now(),

            ]);


            if($app_id){
                DB::table('apps')->where('id',$app_id)->update([
                    'path_app'=>'Production/user-'.$U->id.'/app-'.$app_id.'/'
                ]);
            }

            return redirect()->route('dash.app.detail',['uuid'=>$uuid]);

         }
        return back();
         
         


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
