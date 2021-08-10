<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Auth;
use DB;
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        $U=Auth::check()?Auth::guard('web')->User():null;
        view()->composer('adminlte::page', function ($view)
        {

            Event::listen(BuildingMenu::class, function (BuildingMenu $event)  {


                $event->menu->add([
                    'text' => 'New App',
                    'url' => '',
                    'class'=>'btn btn-success',
                      'topnav_right' => true,

                ]); 
            });

            $U=(Auth::User());
                 Event::listen(BuildingMenu::class, function (BuildingMenu $event) use ($U) {
                     $apps=DB::table('apps')->where('user_id',$U->id)->get();
                        $event->menu->add('INSTANCE');

                         foreach($apps as $app){
                            $event->menu->add([
                                'text' => $app->name,
                                'url' => route('dash.app.detail',['uuid'=>$app->uuid]),
                            ]);
                         }
       
             });
            Event::listen(BuildingMenu::class, function (BuildingMenu $event)  {


                $event->menu->add([
                    'text' => 'Documentation',
                    'url' => '',
                ]); 
            });


            
        }); 

   

       
    }
}
