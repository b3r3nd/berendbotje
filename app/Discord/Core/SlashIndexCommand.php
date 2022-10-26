<?php

namespace App\Discord\Core;

use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\MessageBuilder;

abstract class SlashIndexCommand extends SlashCommand implements PaginationIndex
{
    public int $offset = 0;
    public int $perPage = 10;

    public function nextButton(): Button
    {
        return ButtonFactory::nextButton($this);
    }

    public function previousButton(): Button
    {
        return ButtonFactory::previousButton($this);
    }

    public function action(): MessageBuilder
    {
        $embedBuilder = $this->getEmbed();
        $next = $this->nextButton();
        $previous = $this->previousButton();
        $actionRow = ActionRow::new()->addComponent($previous)->addComponent($next);
        return MessageBuilder::new()->addEmbed($embedBuilder->getEmbed())->addComponent($actionRow);
    }
}
