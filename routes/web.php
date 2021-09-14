<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use Illuminate\Support\Facades\Redis;

Route::get('/publish', function () {
    // ...

    $red= Redis::connection('default');
    // dd(env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'));
    dd($red->publish('app:1:method','new-token'));

});

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::prefix('dashboard')->middleware(['auth:web'])->name('dash')->group(function(){

    Route::prefix('app')->name('.app')->group(function(){

        Route::get('/', [App\Http\Controllers\AppCtrl::class, 'index'])->name('.index');
        Route::get('/{uuid}/wa-file/{number}/{filename}', [App\Http\Controllers\AppCtrl::class, 'get_file'])->name('.get_file');
        Route::get('/new-instance', [App\Http\Controllers\AppCtrl::class, 'new'])->name('.new');

        Route::post('/new-instance', [App\Http\Controllers\AppCtrl::class, 'store'])->name('.store');


        Route::get('/{uuid}/dashboard', [App\Http\Controllers\AppCtrl::class, 'detail'])->middleware('appCheck')->name('.detail');
        Route::get('/{uuid}/messages', [App\Http\Controllers\AppCtrl::class, 'message'])->name('.message');

    });

     Route::prefix('contact')->name('.contact')->group(function(){

        Route::get('/', [App\Http\Controllers\ContactCtrl::class, 'contact'])->name('.index');
        Route::post('/', [App\Http\Controllers\ContactCtrl::class, 'store'])->name('.store');
        Route::put('/{id}', [App\Http\Controllers\ContactCtrl::class, 'update'])->name('.update');
        Route::delete('/{id}', [App\Http\Controllers\ContactCtrl::class, 'delete'])->name('.delete');

     });

     
      Route::prefix('group')->name('.group')->group(function(){

        Route::get('/', [App\Http\Controllers\GroupCtrl::class, 'contact'])->name('.index');
        Route::post('/', [App\Http\Controllers\GroupCtrl::class, 'store'])->name('.store');

        Route::put('/{id}', [App\Http\Controllers\GroupCtrl::class, 'update'])->name('.update');
        Route::delete('/{id}', [App\Http\Controllers\GroupCtrl::class, 'delete'])->name('.delete');


     });



});



Route::get('/home', function(){

    return redirect()->route('dash.app.index');
    
})->name('home');


