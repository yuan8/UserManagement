<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Auth;
use DB;
use Illuminate\Support\Facades\View;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */


  
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(BuildingMenu $event,Request $request)
    {
         



        

         
    }
}
