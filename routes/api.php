<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/my-profile', function (Request $request) {
    return $request->user();
});

Route::prefix('/')->group(function(){

});

Route::prefix('/sanbox')->group(function(){

    Route::prefix('/app/{uuid}')->group(function(){
        Route::prefix('/message')->group(function(){
            Route::get('/',[App\Http\Controllers\MessageCtrl,'index'])->name('.index');
            Route::put('update/{id}',[App\Http\Controllers\MessageCtrl,'update'])->name('.update');
            Route::delete('delete/{id}',[App\Http\Controllers\MessageCtrl,'destroy'])->name('.destroy');
            Route::post('crete/',[App\Http\Controllers\MessageCtrl,'store'])->name('.store');

        })->name('.message');

    });
})->name('sanbox');



