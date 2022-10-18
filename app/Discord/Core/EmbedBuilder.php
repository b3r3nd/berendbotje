<?php

namespace App\Discord\Core;

use Discord\Parts\Embed\Embed;

/**
 * Embeds are used in almost every response, the builder helps is abstract some code :)
 */
class EmbedBuilder
{
    private Embed $embed;

    /**
     * @param $discord
     * @param string $title
     * @param string $footer
     * @param string $description
     * @return Embed
     */
    public static function create($discord, string $title,  string $footer, string $description): Embed
    {
        return (new self($discord, $title, $footer, $description))->getEmbed();
    }

    /**
     * @param $discord
     * @param string $title
     * @param string $footer
     * @param string $description
     */
    public function __construct($discord, string $title, string $footer, string $description)
    {
        $this->embed = new Embed($discord);
        $this->embed->setType('rich');
        $this->embed->setColor(2067276);
        $this->embed->setDescription($description);
        $this->embed->setTitle($title);
        $this->embed->setFooter($footer);
    }

    /**
     * @return Embed
     */
    public function getEmbed(): Embed
    {
        return $this->embed;
    }

    /**
     * @param $description
     * @return void
     */
    public function setDescription($description): void
    {
        $this->embed->setDescription($description);
    }
}
