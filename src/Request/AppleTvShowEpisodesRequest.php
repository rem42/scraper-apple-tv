<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Request;

use Scraper\Scraper\Attribute\Scraper;

/**
 * Episodes of a show, all seasons together, by pages.
 */
#[Scraper(path: 'shows/{id}/episodes')]
class AppleTvShowEpisodesRequest extends AppleTvRequest
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

    /**
     * Page of episodes to fetch: the offset and the number of episodes, at most 100.
     */
    public function setPage(int $offset, int $count = 100): static
    {
        if ($count < 1 || $count > 100) {
            throw new \InvalidArgumentException('Count must be between : 1 to 100');
        }
        $this->parameters['nextToken'] = $offset . ':' . $count;

        return $this;
    }
}
