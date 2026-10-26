<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Model;

class Movie extends Content
{
    /** Runtime, in seconds */
    public ?int $duration = null;

    /** @var array<int, string> */
    public array $directors = [];
}
