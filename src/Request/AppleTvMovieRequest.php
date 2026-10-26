<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Request;

use Scraper\Scraper\Attribute\Scraper;

/**
 * Movie page, by its Apple TV identifier ("umc.cmc.…").
 */
#[Scraper(path: 'movies/{id}')]
class AppleTvMovieRequest extends AppleTvRequest
{
    private string $id;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }
}
