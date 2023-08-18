<?php

namespace App\Discord\Message\Actions;

use App\Discord\Core\Interfaces\Action;
use App\Domain\Message\Models\MentionGroup;
use Discord\Helpers\Collection;

class UpdateMentionGroupAction implements Action
{
    /**
     * @param MentionGroup $mentionGroup
     * @param Collection $options
     */
    public function __construct(
        private readonly MentionGroup $mentionGroup,
        private readonly Collection   $options,
    )
    {
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        if ($this->options->get('name', 'has_role')) {
            $this->mentionGroup->update(['has_role' => true, 'has_user' => false]);
        } else {
            $this->mentionGroup->update(['has_role' => false, 'has_user' => true]);
        }
        if ($this->options->get('name', 'multiplier')) {
            $this->mentionGroup->update(['multiplier' => $this->options->get('name', 'multiplier')]);
        }
    }
}
