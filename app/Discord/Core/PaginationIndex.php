<?php

namespace App\Discord\Core;

use Discord\Builders\Components\Button;
use Discord\Parts\Embed\Embed;

interface PaginationIndex
{
    public function getEmbed(): Embed;

    public function nextButton(): Button;

    public function previousButton(): Button;
}
