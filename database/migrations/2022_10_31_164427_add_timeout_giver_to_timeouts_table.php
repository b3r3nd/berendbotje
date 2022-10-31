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
        Schema::table('timeouts', function (Blueprint $table) {
            $table->unsignedBigInteger('giver_id')->nullable();
            $table->foreign('giver_id')->references('id')->on('discord_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('timeouts', function (Blueprint $table) {
            //
        });
    }
};
