<?php

namespace App\Discord\Core\Builders;

use App\Discord\Core\SlashCommand;
use Discord\Parts\Embed\Embed;
use Exception;

/**
 * Embeds are used in almost every response, the builder helps is abstract some code :)
 */
class EmbedBuilder
{
    private Embed $embed;


    /**
     * @param SlashCommand $command
     * @param string $title
     * @param string $description
     * @return EmbedBuilder
     * @throws Exception
     */
    public static function create(SlashCommand $command, string $title = "", string $description = ""): EmbedBuilder
    {
        return (new self($command, $title, $description));
    }

    /**
     * @param SlashCommand $command
     * @param string $title
     * @param string $description
     * @throws Exception
     */
    public function __construct(SlashCommand $command, string $title = '', string $description = '')
    {
        $this->embed = new Embed($command->discord);
        $this->embed->setType('rich');
        $this->embed->setColor(2067276);
        $this->embed->setDescription($description);
        $this->embed->setTitle($title);
        $this->embed->setTimestamp();
        $this->embed->setFooter($command->interaction->member->username, $command->interaction->member->user->avatar);
    }

    /**
     * @return $this
     */
    public function setFailed(): self
    {
        $this->embed->setColor(15548997);
        return $this;
    }

    /**
     * @return $this
     */
    public function setWarning(): self
    {
        $this->embed->setColor(15105570);
        return $this;
    }

    /**
     * @return $this
     */
    public function setSuccess(): self
    {
        $this->embed->setColor(2067276);
        return $this;
    }

    /**
     * @return $this
     */
    public function setLog(): self
    {
        $this->embed->setColor(3447003);
        return $this;
    }


    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->embed->setTitle($title);
        return $this;
    }

    /**
     * @param string $footer
     * @return $this
     */
    public function setFooter(string $footer): self
    {
        $this->embed->setFooter($footer);
        return $this;
    }

    /**
     * @return Embed
     */
    public function getEmbed(): Embed
    {
        return $this->embed;
    }

    /**
     * @param Embed $embed
     * @return $this
     */
    public function setEmbed(Embed $embed): self
    {
        $this->embed = $embed;
        return $this;
    }

    /**
     * @param $description
     * @return $this
     */
    public function setDescription($description): self
    {
        $this->embed->setDescription($description);
        return $this;
    }

}
