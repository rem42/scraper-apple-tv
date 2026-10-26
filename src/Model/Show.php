<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Model;

class Show extends Content
{
    /** @var array<int, Season> */
    public array $seasons = [];
}
