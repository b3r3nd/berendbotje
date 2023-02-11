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
    /**
     * @var array
     *
     * We keep track of the page offset per user because multiple people can press buttons and in the meantime other users
     * can also retrieve pages and press buttons.
     */
    public array $offset = [];
    public int $perPage = 15;
    public int $total = 0;

    /**
     * @var int
     *
     * The user of the command is usually already available. However, when using interaction commands with buttons, I
     * need to make sure to set the user to whoever last CLICKED a button, in order to show the right data. Not the
     * last person which used the command (which is $this->getCommandUser()). This data is available in the button
     * listener, but I cannot pass it directly through the getEmbed() function (which retrieves the actual data)
     * since its rules are defined in the interface.
     */
    public int $lastUser = 0;

    /**
     * @return MessageBuilder
     */
    public function action(): MessageBuilder
    {
        $this->setLastUser($this->interaction->member->id);
        $this->setOffset($this->interaction->member->id, 0);
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
                // if the person clicking on a button is not the person who received that reaction, don't do anything
                if($interaction->member->id !== $interaction->message->interaction->user->id) {
                    return;
                }

                $this->incOffset($interaction->member->id, $this->perPage);
                $next = $this->nextButton();
                $previous = $this->previousButton();
                if (($this->getOffset($interaction->member->id) + $this->perPage) >= $this->total) {
                    $next->setDisabled(true);
                }
                if ($this->getOffset($interaction->member->id) > 0) {
                    $previous->setDisabled(false);
                }
                $actionRow = ActionRow::new()->addComponent($previous)->addComponent($next);
                $this->setLastUser($interaction->member->id);
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
                // if the person clicking on a button is not the person who received that reaction, don't do anything
                if($interaction->member->id !== $interaction->message->interaction->user->id) {
                    return;
                }

                $this->decOffset($interaction->member->id, $this->perPage);

                $next = $this->nextButton();
                $previous = $this->previousButton();
                if ($this->getOffset($interaction->member->id) === 0) {
                    $next->setDisabled(false);
                    $previous->setDisabled(true);
                }
                $actionRow = ActionRow::new()->addComponent($previous)->addComponent($next);
                $this->setLastUser($interaction->member->id);
                $interaction->message->edit(MessageBuilder::new()->addEmbed($this->getEmbed())->addComponent($actionRow));

            }, Bot::getDiscord());
    }

    /**
     * @param int $lastUser
     * @return void
     */
    public function setLastUser(int $lastUser): void
    {
        $this->lastUser = $lastUser;
    }

    /**
     * @return int
     */
    public function getLastUser(): int
    {
        return $this->lastUser;
    }


    /**
     * @param int $memberId
     * @return int
     */
    public function getOffset(int $memberId): int
    {
        return $this->offset[$memberId];
    }

    /**
     * @param int $memberId
     * @param int $offset
     * @return void
     */
    public function setOffset(int $memberId, int $offset): void
    {
        $this->offset[$memberId] = $offset;

    }

    /**
     * @param int $memberId
     * @param int $amount
     * @return void
     */
    public function incOffset(int $memberId, int $amount): void
    {
        $this->offset[$memberId] = $this->offset[$memberId] += $amount;
    }

    /**
     * @param int $memberId
     * @param int $amount
     * @return void
     */
    public function decOffset(int $memberId, int $amount): void
    {
        $this->offset[$memberId] -= $amount;
    }
}
