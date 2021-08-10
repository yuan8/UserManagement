<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use DB;
class RuningApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wa:run {id} {--kill}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runing  whatsapp egine wa:run {id application}';

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
       

          // $processFind =  Process::fromShellCommandline('sudo whoami');
          //   $processFind->run();
          //   dd($processFind->getOutput());
        

        $app_id=$this->argument('id')??0;
        $app=DB::table('apps')->where('id',$app_id)
        ->first();
            $listing_pid=[];

        if($app){

            for ($i=0; $i <3 ; $i++) { 
                $f='sudo ps aux | grep  "nodemon[ ].*.user-'.$app->user_id.'/app-'.$app->id.'/app.js"';

        
                    $processFind =  Process::fromShellCommandline($f);
                    $processFind->run();
                    // while ($processFind->isRunning()) {
                    //     dd($processFind->getPid(),$processFind->getOutput());
                    //     // code...
                    // }
                    // dd($processFind->getPid(),$f,Process::isTtySupported(),$processFind->getOutput());
                    if($processFind->getOutput()){

                        $processFind=explode("\n",$processFind->getOutput());
                        if(count($processFind)){
                            $re=null;
                            foreach($processFind as $p){
                                if(strpos($p,'app/WaBot/Production/user-'.$app->user_id.'/app-'.$app->id.'/app.js')!==false){
                                    $re=$p;
                                    $this->info($p);
                                }
                            }
                            if($re){
                                $processFind=[];
                                $processFind=array_values(array_filter(explode(' ', $re)));
                            }

                        }else{
                            $processFind=[];
                        }

                        // $this->info($processFind[1],'pid');


                        if(isset($processFind[1])){
                            $listing_pid[]=$processFind[1];
                           // $kill= shell_exec('kill -15 '.$processFind[1]);
                           // (posix_kill((int)$processFind,-15));
                            $killProcess =  Process::fromShellCommandline('sudo kill -15 '.$processFind[1]);
                            $killProcess->run();
                            
                        }
                    }
            }
            sleep(1);

            if(($this->options()['kill'])){

                return $app->name." kill";
                

            }else{

                $C='nodemon --watch '.app_path('WaBot/Production/user-'.$app->user_id.'/app-'.$app->id.'/app.js'.' '.app_path('WaBot/Production/user-'.$app->user_id.'/app-'.$app->id.'/app.js'));
               
                  $p=  shell_exec($C." >/dev/null >/dev/null &");
                 
               

               
                return $app->name. " new initialized ";

            }

            
        }

      
        
        return 0;
    }
}
