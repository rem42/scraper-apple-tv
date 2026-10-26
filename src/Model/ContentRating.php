<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Model;

/**
 * Age rating of the storefront, e.g. "-12" in the "fr_tv" system.
 */
class ContentRating
{
    public ?string $name = null;
    public ?string $displayName = null;
    public ?string $system = null;
    public ?int $value = null;
}
