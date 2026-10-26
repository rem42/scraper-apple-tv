<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Api;

use Scraper\Scraper\Exception\ScraperException;
use Scraper\ScraperAppleTv\Model\Movie;

class AppleTvMovieApi extends AbstractAppleTvApi
{
    public function execute(): object|array|bool|string
    {
        $data = $this->decode();

        if (!\is_array($data['content'] ?? null) || 'Movie' !== ($data['content']['type'] ?? null)) {
            throw new ScraperException('Apple TV item is not a movie');
        }

        $movie = new Movie();
        $this->hydrateContent($movie, $data['content']);

        if (\is_array($data['badges'] ?? null)) {
            $movie->badges = $this->createBadges($data['badges']);
        }

        return $movie;
    }
}
