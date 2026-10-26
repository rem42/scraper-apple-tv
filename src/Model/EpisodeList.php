<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Model;

class EpisodeList
{
    /** Episodes of the show, all pages together */
    public ?int $totalEpisodeCount = null;

    /** @var array<int, Episode> */
    public array $episodes = [];

    /** Token of the next page ("offset:count"), null on the last one */
    public ?string $nextToken = null;
}
