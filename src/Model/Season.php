<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Model;

class Season
{
    public ?string $id = null;
    public ?string $title = null;
    public ?int $seasonNumber = null;
    public ?\DateTimeImmutable $releaseDate = null;
    public ?string $url = null;
}
