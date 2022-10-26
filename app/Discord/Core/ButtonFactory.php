<?php

namespace App\Discord\Core;

use Discord\Builders\Components\Button;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;

class ButtonFactory
{
    /**
     * @param PaginationIndex $index
     * @return Button
     */
    public static function nextButton(PaginationIndex $index): Button
    {
        return Button::new(Button::STYLE_PRIMARY)
            ->setLabel(__('bot.buttons.next'))
            ->setListener(function (Interaction $interaction) use ($index) {
                $index->offset += $index->perPage;
                $embedBuilder = $index->getEmbed();
                $interaction->message->edit(MessageBuilder::new()->addEmbed($embedBuilder->getEmbed()));
            }, Bot::getDiscord());
    }

    /**
     * @param PaginationIndex $index
     * @return Button
     */
    public static function previousButton(PaginationIndex $index): Button
    {
        return Button::new(Button::STYLE_PRIMARY)
            ->setLabel(__('bot.buttons.previous'))
            ->setListener(function (Interaction $interaction) use ($index) {
                $index->offset -= $index->perPage;
                $embedBuilder = $index->getEmbed();
                $interaction->message->edit(MessageBuilder::new()->addEmbed($embedBuilder->getEmbed()));
            }, Bot::getDiscord());
    }
}
