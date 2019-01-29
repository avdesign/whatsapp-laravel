<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatGroupInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_group_invitations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug');
            $table->integer('total')->unsigned();
            $table->integer('remaining')->unsigned();
            $table->date('expires_at')->nullable();
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('chat_groups');
            $table->softDeletes();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_group_invitations');
    }
}
