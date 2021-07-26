<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your \application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/my-profile', function (Request $request) {
    return $request->user();
});

Route::prefix('/')->group(function(){

});

Route::prefix('/sanbox')->name('sanbox')->group(function(){

    Route::prefix('/app/{uuid}')->group(function(){
        Route::prefix('/message')->name('.message')->group(function(){
            Route::get('/',[App\Http\Controllers\MessageCtrl::class,'index'])->name('.index');
            Route::put('update/{id}',[App\Http\Controllers\MessageCtrl::class,'update'])->name('.update');
            Route::delete('delete/{id}',[App\Http\Controllers\MessageCtrl::class,'destroy'])->name('.destroy');
            Route::post('crete/',[App\Http\Controllers\MessageCtrl::class,'store'])->name('.store');

        });

    });
});



