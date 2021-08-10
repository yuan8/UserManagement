<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class App extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('apps', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('name');
            $table->string('path_app');
            $table->integer('wa_status')->default(0);
            $table->integer('wa_pid')->nullable();
            $table->string('wa_number')->nullable();
            $table->string('wa_state')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->integer('status_active')->default(1);
            $table->string('host_attemp')->nullable();
            $table->string('host_receive')->nullable();
            $table->string('token_access')->unique();
            $table->dateTime('active_until')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
            $table->unique(['wa_number','user_id']);
            $table->unique(['name','user_id']);

            $table->foreign('user_id')
            ->references('id')
              ->on('users')
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
        Schema::dropIfExists('apps');

    }
}
