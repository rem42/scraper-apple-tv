<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Request;

use Scraper\Scraper\Attribute\Scraper;

/**
 * Search of the Apple TV catalog (Apple Originals and channels): titles only sold on the iTunes Store are not found.
 */
#[Scraper(path: 'search')]
class AppleTvSearchRequest extends AppleTvRequest
{
    public function setTerm(string $term): static
    {
        $this->parameters['searchTerm'] = $term;

        return $this;
    }
}
