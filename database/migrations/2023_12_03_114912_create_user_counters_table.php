<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_counters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guild_id');
            $table->foreign('guild_id')->references('id')->on('guilds');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('discord_users');
            $table->integer('count');
            $table->integer('highest_count');
            $table->integer('fail_count')->nullable();
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
        Schema::dropIfExists('user_counters');
    }
};
