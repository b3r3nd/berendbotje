<?php

namespace App\Discord\Core;

use App\Discord\Core\Interfaces\PaginationIndex;
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
abstract class SlashIndexCommand extends SlashCommand implements PaginationIndex
{
    public array $offset = [];
    public int $perPage = 1;
    public int $total = 0;


    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset[$this->getCommandUser()];

    }

    /**
     * @param int $offset
     * @return void
     */
    public function setOffset(int $offset): void
    {
        $this->offset[$this->getCommandUser()] = $offset;

    }

    /**
     * @param int $amount
     * @return void
     */
    public function incOffset(int $amount): void
    {
        $this->offset[$this->getCommandUser()] = $this->offset[$this->getCommandUser()] += $amount;
    }

    /**
     * @return MessageBuilder
     */
    public function action(): MessageBuilder
    {
        $this->setOffset(0);
        $embed = $this->getEmbed();
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
                $this->incOffset($this->perPage);
                $next = $this->nextButton();
                $previous = $this->previousButton();
                if (($this->getOffset() + $this->perPage) > $this->total) {
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
                $this->incOffset($this->perPage);
                $next = $this->nextButton();
                $previous = $this->previousButton();
                if ($this->getOffset() === 0) {
                    $next->setDisabled(false);
                    $previous->setDisabled(true);
                }
                $actionRow = ActionRow::new()->addComponent($previous)->addComponent($next);
                $interaction->message->edit(MessageBuilder::new()->addEmbed($this->getEmbed())->addComponent($actionRow));

            }, Bot::getDiscord());
    }
}
