<?php

namespace App\Discord;

use App\Discord\Core\Command\MessageCommand;
use App\Discord\Core\Permission;
use App\Models\DiscordUser;
use App\Models\Guild;
use App\Models\KickCounter;

class TestCommand extends MessageCommand
{

    public function permission(): Permission
    {
        return Permission::NONE;
    }

    public function trigger(): string
    {
        return 'test';
    }

    public function action(): void
    {
//        $user = DiscordUser::get($this->commandUser);
//        $guild = Guild::get($this->guildId);
//
//        $kickCounter = $user->kickCounters()->where('guild_id', $guild->id)->get();
//        if ($kickCounter->isEmpty()) {
//            $user->kickCounters()->save(new KickCounter(['count' => 1, 'guild_id' => $guild->id]));
//        } else {
//            $kickCounter = $kickCounter->first();
//            $kickCounter->update(['count' => $kickCounter->count + 1]);
//        }
    }
}
