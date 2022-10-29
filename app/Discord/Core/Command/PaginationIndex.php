<?php

namespace App\Discord\Core\Command;

use Discord\Builders\Components\Button;
use Discord\Parts\Embed\Embed;

/**
 * Kinda leftover from my previous structure, these could be abstract functions in @see SlashIndexCommand. However
 * I foresee a future where I might still need this so might as wel keep it as is.
 */
interface PaginationIndex
{
    public function getEmbed(): Embed;

    public function nextButton(): Button;

    public function previousButton(): Button;
}
