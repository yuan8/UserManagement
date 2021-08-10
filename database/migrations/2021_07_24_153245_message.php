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
            $table->string('message_id')->nullable();
            $table->bigInteger('app_id')->unsigned();
            $table->tinyInteger('message_type')->default(1);
            $table->bigInteger('group_id')->nullable()->unsigned();
            $table->string('from_number')->nullable();
            $table->string('to_number');
            $table->mediumText('content_text')->nullable();
            $table->mediumText('content_attach')->nullable();
            $table->integer('status')->default(0);
            $table->dateTime('send_date')->nullable();
            $table->boolean('use_forward')->nullable()->default(0);
            $table->bigInteger('id_message_forward')->unsigned()->nullable();
            $table->timestamps();
            
            $table->foreign('app_id')
            ->references('id')
              ->on('apps')
              ->onUpdate('cascade')
              ->onDelete('cascade');

            $table->foreign('id_message_forward')
            ->references('id')
              ->on('messages')
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
