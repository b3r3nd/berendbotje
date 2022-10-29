<?php

namespace App\Discord\Core\Command;

use App\Discord\Core\Bot;
use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;

/**
 * Extendable class to easily create new index commands for both message and slash commands. Read the property
 * description, so you know what you are doing.
 *
 * @property $offset        Automatically increased by te perPage amount when clicking to next page.
 * @property $perPage       Can be overwritten in child to set the items shown per page.
 * @property $total         MUST BE SET in child in order to disable buttons when you are on the last page.
 */
abstract class IndexCommand extends SlashAndMessageCommand implements PaginationIndex
{
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
