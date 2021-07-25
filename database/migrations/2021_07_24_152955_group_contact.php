<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GroupContact extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
         Schema::create('group_contacts', function (Blueprint $table) {
            $table->bigInteger('group_id')->unsigned();
            $table->bigInteger('contact_id')->unsigned();
            $table->unique(['group_id','contact_id']);
            $table->timestamps();

            $table->foreign('group_id')
            ->references('id')
              ->on('groups')
              ->onUpdate('cascade')
              ->onDelete('cascade');

             $table->foreign('contact_id')
            ->references('id')
              ->on('contacts')
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
        Schema::dropIfExists('group_contacts');

    }
}
