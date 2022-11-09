<?php

namespace App\Observers;

use App\Discord\Core\Bot;
use App\Discord\Helper;
use App\Models\MessageCounter;

class MessageObserver
{
    /**
     * Handle the MessageCounter "created" event.
     *
     * @param  \App\Models\MessageCounter  $messageCounter
     * @return void
     */
    public function created(MessageCounter $messageCounter): void
    {
       //
    }

    /**
     * Handle the MessageCounter "updated" event.
     *
     * @param  \App\Models\MessageCounter  $messageCounter
     * @return void
     */
    public function updated(MessageCounter $messageCounter): void
    {
      //
    }

    /**
     * Handle the MessageCounter "deleted" event.
     *
     * @param  \App\Models\MessageCounter  $messageCounter
     * @return void
     */
    public function deleted(MessageCounter $messageCounter): void
    {
      //
    }

    /**
     * Handle the MessageCounter "restored" event.
     *
     * @param  \App\Models\MessageCounter  $messageCounter
     * @return void
     */
    public function restored(MessageCounter $messageCounter): void
    {
      //
    }

    /**
     * Handle the MessageCounter "force deleted" event.
     *
     * @param  \App\Models\MessageCounter  $messageCounter
     * @return void
     */
    public function forceDeleted(MessageCounter $messageCounter): void
    {
      //
    }
}
