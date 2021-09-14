<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use Storage;
use Carbon\Carbon;
use DB;
class AppMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $U=Auth::User();

        $apps=DB::table('apps')->where('user_id',$U->id)->get();
        foreach ($apps as $key => $app) {
            $master=scandir(storage_path('app/master_app'));
             Storage::disk('disk_app')->put('Production/user-'.$U->id.'/app-'.$app->id.'/downloads/index.html','path err');
            foreach($master??[] as $m_file){
                if($m_file=='app.js'){
                    if(!file_exists(app_path('WaBot/Production/user-'.$U->id.'/app-'.$app->id.'/'.$m_file ))){
                            $content=file_get_contents(storage_path('app/master_app/'.$m_file));
                            $content=
                            'var the_dirname="'.app_path('WaBot/Production/user-'.$U->id.'/app-'.$app->id).'";
                            var app_data={id:'.$app->id.',name:"wabot-app-'.$app->id.'"};
                            '.$content;
                            Storage::disk('disk_app')->put('Production/user-'.$U->id.'/app-'.$app->id.'/'.$m_file,$content);
                    }
                }else if($m_file=='app.setting.json'){
                    if(!file_exists(app_path('WaBot/Production/user-'.$U->id.'/app-'.$app->id.'/'.$m_file))){
                        $content=[
                            'app_id'=>$app->id,
                            'user_id'=>$U->id
                        ];
                         Storage::disk('disk_app')->put('Production/user-'.$U->id.'/app-'.$app->id.'/'.$m_file,json_encode($content));
                     }
                }else if($m_file=='app.status.json'){
                    if(!file_exists(app_path('WaBot/Production/user-'.$U->id.'/app-'.$app->id.'/'.$m_file))){
                        $content=[

                             "browser_open"=>false,
                            "wa_state"=>null,
                            "wa_number"=>null,
                            "pid_process"=>null,
                            "status_client"=>null,
                            "qr_login"=>null,
                            "pid"=>null,
                            "app_id"=>null,
                            "qr_code"=>null,
                            'update_at'=>Carbon::now()
                        ];
                         Storage::disk('disk_app')->put('Production/user-'.$U->id.'/app-'.$app->id.'/'.$m_file,json_encode($content));
                     }

                }
            }

            shell_exec('sudo chmod -R 777 '.app_path('WaBot/'));
        }

        return $next($request);

    }
}
