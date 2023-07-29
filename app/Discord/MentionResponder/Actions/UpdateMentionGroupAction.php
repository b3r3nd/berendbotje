<?php

namespace App\Discord\MentionResponder\Actions;

use App\Discord\Core\Interfaces\Action;
use App\Discord\MentionResponder\Models\MentionGroup;
use Discord\Helpers\Collection;

/**
 * @property Collection $options  List of options retrieved from the slash command which triggered this action
 * @property MentionGroup $mentionGroup Mention group to update
 */
class UpdateMentionGroupAction implements Action
{
    private Collection $options;
    private MentionGroup $mentionGroup;

    /**
     * @param MentionGroup $mentionGroup
     * @param Collection $options
     */
    public function __construct(MentionGroup $mentionGroup, Collection $options)
    {
        $this->options = $options;
        $this->mentionGroup = $mentionGroup;

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
