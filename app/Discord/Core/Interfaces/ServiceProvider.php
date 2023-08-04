<?php

namespace App\Discord\Core\Interfaces;

use App\Discord\Core\Bot;

interface ServiceProvider
{
    /**
     * Function is called before the bot is ready.
     *
     * @param Bot $bot
     * @return void
     */
    public function boot(Bot $bot): void;

    /**
     * Executes when bot is ready on `init`.
     *
     * @param Bot $bot
     * @return void
     */
    public function init(Bot $bot): void;

}
