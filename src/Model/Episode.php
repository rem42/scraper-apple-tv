<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Model;

class Episode
{
    public ?string $id = null;
    public ?string $title = null;
    public ?string $description = null;
    public ?int $seasonNumber = null;
    public ?int $episodeNumber = null;

    /** Runtime, in seconds */
    public ?int $duration = null;
    public ?\DateTimeImmutable $releaseDate = null;
    public ?string $url = null;
}
