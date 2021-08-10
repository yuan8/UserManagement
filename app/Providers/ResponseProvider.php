<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Response;

class ResponseProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    // public function register()
    // {
    //     //
    // }

    // *
    //  * Bootstrap services.
    //  *
    //  * @return void
     
    // public function boot()
    // {
    //     //
    // }

    public static function response($data,$code){

        $content=['code'=>$code,
            'data'=>$data['data'],
            'mesaage'=>$data['message']
        ];


       return  (new Response($content, $code))
              ->header('Content-Type', 'application/json');
   
    }
}
