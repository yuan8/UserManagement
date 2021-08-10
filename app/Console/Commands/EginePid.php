<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class EginePid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wa:pid {--list} {{--kill=}} {{--kill-all}}';

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
        $key=array_keys($this->options());
        $options=$this->options()??[];
        $options=array_filter($options,function($val,$key){
           return (in_array($key,['list','kill','kill-all']) and $val);
        },ARRAY_FILTER_USE_BOTH);
        $noption=false;
        $options_keys=array_keys($options);

        foreach($options_keys??[] as $op){
             switch($op){
                case 'list':
                    $p=Process::fromShellCommandline('sudo ps aux | grep  "node.*.user-.*./app.js"');
                    $p->run();
                    $p=explode("\n",$p->getOutput()??"")??[];
                    $output=[];
                    foreach($p as $x){
                        if($x and !(strpos($x,'node.*.user-.*./app.js')!==false) ){
                            $x=array_values(array_filter(explode(' ',$x)));
                            $output[$x[1]]=isset($x[12])?$x[12]:$x[11];
                        }
                        
                    }

                    $this->info('PID LIST ');
                    $this->info('--------------');
                    $this->info(json_encode($output,JSON_PRETTY_PRINT));

                break;
                case 'kill':
                      $killProcess =  Process::fromShellCommandline('sudo kill -15 '.$options['kill']);
                        $killProcess->run();
                        $p=explode("\n",$killProcess->getOutput()??"")??[];
                        $output=[];
                        foreach($p as $x){
                            if($x){
                                $x=array_values(array_filter(explode(' ',$x)));
                                $output[$x[1]]=$x[12];
                            }
                            
                        }

                        $this->info('try killed pid '.$options['kill']);
                        $this->info('--------------');
                        $this->info(json_encode($output,JSON_PRETTY_PRINT));
                break;
                case 'kill-all':
                        $p=Process::fromShellCommandline('sudo ps aux | grep  "node.*.user-.*./app.js"');
                        $p->run();
                        $p=explode("\n",$p->getOutput()??"")??[];
                        $output=[];
                        foreach($p as $x){
                            if($x and !(strpos($x,'node.*.user-.*./app.js')!==false) ){
                                $x=array_values(array_filter(explode(' ',$x)));
                                $output[$x[1]]=isset($x[12])?$x[12]:$x[11];
                            }
                            
                        }

                        foreach($output as $pid=>$file){
                            $killProcess =  Process::fromShellCommandline('sudo kill -15 '.$pid);
                            $killProcess->run();

                        }
                        sleep(3);

                        foreach($output as $pid=>$file){
                            $killProcess =  Process::fromShellCommandline('sudo kill -9 '.$pid);
                            $killProcess->run();

                        }


                        $this->info('try killed pid :'.implode('|',array_keys($output)));
                        $this->info('--------------');
                break;
                default:
                    $noption=true;
                break;
            }
        }

        if(count($options)<=0){
            $this->info('no options');
        }

        return 0;
    }
}
