<?php

namespace App\Discord\Core;

use Discord\Builders\Components\Button;

interface PaginationIndex
{
    public function getEmbed(): EmbedBuilder;

    public function nextButton(): Button;

    public function previousButton(): Button;
}
