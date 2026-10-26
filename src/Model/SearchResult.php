<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Model;

class SearchResult
{
    public ?string $term = null;

    /** True when Apple found nothing for the term: it then suggests popular titles, which are left out of $items. */
    public bool $isMissingContent = false;

    /**
     * Movies and shows matching the term, without duplicates.
     *
     * @var array<int, Movie|Show>
     */
    public array $items = [];
}
