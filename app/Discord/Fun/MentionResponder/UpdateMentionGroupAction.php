<?php

namespace App\Discord\Fun\MentionResponder;

use App\Discord\Core\Interfaces\Action;
use App\Models\MentionGroup;

class UpdateMentionGroupAction implements Action
{
    private array $arguments;
    private MentionGroup $mentionGroup;


    public function __construct(MentionGroup $mentionGroup, array $arguments)
    {
        $this->arguments = $arguments;
        $this->mentionGroup = $mentionGroup;

    }

    public function execute(): void
    {
        if ($this->arguments[1] === 'has_role') {
            $this->mentionGroup->update(['has_role' => true, 'has_user' => false]);
        } else {
            $this->mentionGroup->update(['has_role' => false, 'has_user' => true]);
        }
        if ($this->arguments[2]) {
            $this->mentionGroup->update(['multiplier' => $this->arguments[2]]);
        }
    }
}
