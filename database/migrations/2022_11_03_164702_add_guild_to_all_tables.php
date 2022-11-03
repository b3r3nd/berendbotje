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
        Schema::table('discord_users', function (Blueprint $table) {
            $table->string('guild_id');
        });
        Schema::table('commands', function (Blueprint $table) {
            $table->string('guild_id');
        });
        Schema::table('mediachannels', function (Blueprint $table) {
            $table->string('guild_id');
        });
        Schema::table('reactions', function (Blueprint $table) {
            $table->string('guild_id');
        });
        Schema::table('settings', function (Blueprint $table) {
            $table->string('guild_id');
        });
        Schema::table('songs', function (Blueprint $table) {
            $table->string('guild_id');
        });
        Schema::table('timeouts', function (Blueprint $table) {
            $table->string('guild_id');
        });
        Schema::table('emotes', function (Blueprint $table) {
            $table->string('guild_id');
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
    }
};
