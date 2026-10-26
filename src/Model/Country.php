<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Model;

class Country
{
    /** ISO 3166-1 alpha-2 code, e.g. "US" */
    public ?string $code = null;

    /** Name in the requested locale, e.g. "États-Unis" */
    public ?string $name = null;
}
