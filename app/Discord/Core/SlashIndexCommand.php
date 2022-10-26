<?php

namespace App\Discord\Core;

use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;

/**
 * This class can be extended in order to easily add pagination to the index file. All you need to do
 * is write the embed body in the child class, everything else is handled by either this class or SlashCommand.
 */
abstract class SlashIndexCommand extends SlashCommand implements PaginationIndex
{
    /**
     * These can be overwritten in the child, $total must be set in order for the buttons to disable properly.
     */
    public int $offset = 0;
    public int $perPage = 10;
    public int $total = 0;

    /**
     * @return MessageBuilder
     */
    public function action(): MessageBuilder
    {
        $embed = $this->getEmbed();
        $this->offset = 0;
        $next = $this->nextButton();
        $previous = $this->previousButton()->setDisabled(true);

        if ($this->perPage >= $this->total) {
            $next->setDisabled(true);
        }

        $actionRow = ActionRow::new()->addComponent($previous)->addComponent($next);
        return MessageBuilder::new()->addEmbed($embed)->addComponent($actionRow);
    }

    /**
     * @return Button
     */
    public function nextButton(): Button
    {
        return Button::new(Button::STYLE_PRIMARY)
            ->setLabel(__('bot.buttons.next'))
            ->setListener(function (Interaction $interaction) {
                if ($interaction->user->id != $this->getCommandUser()) {
                    return;
                }
                $this->offset += $this->perPage;
                $next = $this->nextButton();
                $previous = $this->previousButton();
                if (($this->offset + $this->perPage) > $this->total) {
                    $next->setDisabled(true);
                }
                if ($this->offset > 0) {
                    $previous->setDisabled(false);
                }
                $actionRow = ActionRow::new()->addComponent($previous)->addComponent($next);
                $interaction->message->edit(MessageBuilder::new()->addEmbed($this->getEmbed())->addComponent($actionRow));

            }, Bot::getDiscord());
    }

    /**
     * @return Button
     */
    public function previousButton(): Button
    {
        return Button::new(Button::STYLE_PRIMARY)
            ->setLabel(__('bot.buttons.previous'))
            ->setListener(function (Interaction $interaction) {
                if ($interaction->user->id != $this->getCommandUser()) {
                    return;
                }
                $this->offset -= $this->perPage;
                $next = $this->nextButton();
                $previous = $this->previousButton();
                if ($this->offset == 0) {
                    $next->setDisabled(false);
                    $previous->setDisabled(true);
                }
                $actionRow = ActionRow::new()->addComponent($previous)->addComponent($next);
                $interaction->message->edit(MessageBuilder::new()->addEmbed($this->getEmbed())->addComponent($actionRow));

            }, Bot::getDiscord());

    }
}
