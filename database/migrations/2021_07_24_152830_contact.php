<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Contact extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
         Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('profile_path')->nullable();
            $table->string('number_wa');
            $table->bigInteger('app_id')->unsigned();
            $table->timestamps();
            $table->unique(['app_id','number_wa']);

             $table->foreign('app_id')
            ->references('id')
              ->on('apps')
              ->onUpdate('cascade')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('contacts');

    }
}
