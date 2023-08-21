<?php

namespace App\Console;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class SlashManager
{
    /**
     * @param string|null $url
     * @param string|null $globalUrl
     * @param string|null $guildUrl
     * @param PendingRequest|null $http
     * @param array|null $globalCommands
     * @param array|null $guildCommands
     */
    public function __construct(
        private ?string         $url = null,
        private ?string         $globalUrl = null,
        private ?string         $guildUrl = null,
        private ?PendingRequest $http = null,
        private ?array          $globalCommands = null,
        private ?array          $guildCommands = null,
    )
    {
        $this->url ??= config('discord.api') . 'applications/' . config('discord.app-id');
        $this->globalUrl ??= $this->url . '/commands';
        $this->guildUrl ??= $this->url . '/guilds/' . config('discord.support-guild') . '/commands';
        $this->http ??= Http::withHeaders(['Authorization' => "Bot " . config('discord.token')]);
        $this->guildCommands ??= config('commands.guild');
        $this->globalCommands ??= config('commands.global');
    }

    /**
     * @return array
     */
    public function register(): array
    {
        $globalCommands = $this->loadCommands($this->globalCommands);
        $guildCommands = $this->loadCommands($this->guildCommands, true);
        return array_merge($guildCommands, $globalCommands);
    }

    /**
     * @param array $commands
     * @param bool $guild
     * @return array
     */
    public function loadCommands(array $commands, bool $guild = false): array
    {
        $commandList = [];
        foreach ($commands as $mainCommand => $subGroups) {
            $subGroupOptions = [];
            foreach ($subGroups as $subGroup => $subCommands) {
                if (is_array($subCommands)) {
                    $subCommandOptions = [];
                    foreach ($subCommands as $subCommand) {
                        $subCommandOptions[] = $this->initCommandOptions($subCommand);
                    }
                    $subGroupOptions[] = [
                        'name' => $subGroup,
                        'description' => $subGroup,
                        'type' => 2,
                        'options' => $subCommandOptions,
                    ];
                } else {
                    $subGroupOptions[] = $this->initCommandOptions($subCommands);
                }
            }
            $optionsArray = [
                'name' => $mainCommand,
                'description' => $mainCommand,
                'options' => $subGroupOptions,
            ];
            $url = $this->globalUrl;
            if ($guild) {
                $url = $this->guildUrl;
            }

            $this->post($url, $optionsArray);
            $commandList[] = $optionsArray['name'];
        }
        return $commandList;
    }

    /**
     * @param string $url
     * @param array $optionsArray
     * @return void
     */
    private function post(string $url, array $optionsArray): void
    {
        $result = $this->http->post($url, $optionsArray)->json();
        if (isset($result['message'], $result['retry_after'])) {
            sleep($result['retry_after']);
            $this->post($url, $optionsArray);
        }
    }

    /**
     * @param int|string $id
     * @param bool $guild
     * @return void
     */
    public function delete(int|string $id, bool $guild = false): void
    {
        $result = $guild ? $this->http->delete($this->guildUrl . '/' . $id) : $this->http->delete($this->globalUrl . '/' . $id);
        if (isset($result['message'], $result['retry_after'])) {
            sleep($result['retry_after']);
            $this->delete($id);
        }
    }

    /**
     * @param $command
     * @return array
     */
    private function initCommandOptions($command): array
    {
        $instance = new $command();
        $options = [
            'name' => $instance->trigger,
            'description' => $instance->description,
            'type' => 1,
        ];
        if (isset($instance->slashCommandOptions)) {
            $options['options'] = $instance->slashCommandOptions;
        }
        return $options;
    }

    /**
     * @return array
     */
    public function commands(): array
    {
        return array_merge($this->guildCommands(), $this->globalCommands());
    }

    /**
     * @return array
     */
    public function guildCommands(): array
    {
        return $this->http->get($this->guildUrl)->json();
    }

    /**
     * @return array
     */
    public function globalCommands(): array
    {
        return $this->http->get($this->globalUrl)->json();
    }
}
