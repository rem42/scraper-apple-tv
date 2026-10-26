<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Model;

/**
 * Rotten Tomatoes scores, only given for some titles (rarely for Apple Originals).
 */
class RottenTomatoes
{
    /** Critics score, in percent */
    public ?int $tomatometerPercentage = null;

    /** e.g. "CertifiedFresh", "Fresh", "Rotten" */
    public ?string $tomatometerFreshness = null;

    /** Audience score, in percent */
    public ?int $audienceScore = null;

    /** Average critics rating, out of 10 */
    public ?float $averageRating = null;
    public ?int $numFreshReviews = null;
    public ?int $numRottenReviews = null;
    public ?string $consensus = null;
    public ?string $url = null;
}
