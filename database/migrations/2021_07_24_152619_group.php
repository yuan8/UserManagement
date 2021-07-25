<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Group extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

         Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('app_id')->unsigned();
            $table->string('profile_path')->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('groups');

    }
}
