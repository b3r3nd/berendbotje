<?php

namespace App\Discord\MentionResponder\Actions;

use App\Discord\Core\Interfaces\Action;
use App\Discord\MentionResponder\Models\MentionGroup;
use Discord\Repository\Interaction\OptionRepository;

class UpdateMentionGroupAction implements Action
{
    private OptionRepository $options;
    private MentionGroup $mentionGroup;


    public function __construct(MentionGroup $mentionGroup, OptionRepository $options)
    {
        $this->options = $options;
        $this->mentionGroup = $mentionGroup;

    }

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
