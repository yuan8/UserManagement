<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Message extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('app_id')->unsigned();
            $table->bigInteger('group_id')->nullable()->unsigned();
            $table->string('from_number')->nullable();
            $table->string('to_number');
            $table->mediumText('content_text');
            $table->mediumText('content_attach')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('messages');

    }
}
