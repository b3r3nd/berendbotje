<?php

namespace App\Discord\Core;

use Discord\Parts\Embed\Embed;

class EmbedBuilder
{
    private Embed $embed;


    public static function create($discord, string $title,  string $footer, string $description): Embed
    {
        return (new self($discord, $title, $footer, $description))->getEmbed();
    }

    public function __construct($discord, string $title, string $footer, string $description)
    {
        $this->embed = new Embed($discord);
        $this->embed->setType('rich');
        $this->embed->setColor(2067276);
        $this->embed->setDescription($description);
        $this->embed->setTitle($title);
        $this->embed->setFooter($footer);
    }

    public function getEmbed(): Embed
    {
        return $this->embed;
    }

    public function setDescription($description) {
        $this->embed->setDescription($description);
    }
}
