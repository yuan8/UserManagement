<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
class CronApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wa:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $kill_app=[];
        $restart_app=[];
        $app=DB::table('apps')->where([
           ['status_active','=',1],
        ])->orWhere([
            ['status_active','=',0],
            ['active_until','<=',Carbon::now()],
        ])->get();

        foreach($app as $ap){
            if(Carbon::parse($ap->active_until)->lte(Carbon::now())){
                $kill_app[]=['user'=>$ap->user_id,'app'=>$ap->id];
                    
                    DB::table('apps')->where(['id'=>$ap->id])->update([
                        'status_app'=>0,
                        'status_active'=>0,
                        'updated_at'=>Carbon::now()
                    ]);

            }else{

                if(Carbon::parse($ap->updated_at)->lte(Carbon::now()->addMinutes(-15))){
                    $restart_app[]=['user'=>$ap->user_id,'app'=>$ap->id];
                }
            }
        }

        foreach($restart_app as $app){

            $p=Artisan::call('wa:run',['id'=>$app['app']]);
             $this->info($p);
        }

        foreach($kill_app as $app){

           $p= Artisan::call('wa:run',['id'=>$app['app'],'--kill'=>true]);
            $this->info($p);

        }

        return 0;
    }
}
