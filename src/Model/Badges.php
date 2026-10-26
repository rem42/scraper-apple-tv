<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Model;

/**
 * Best quality available on Apple TV.
 */
class Badges
{
    /** e.g. "UHD", "HD" */
    public ?string $videoQuality = null;

    /** e.g. "DolbyVisionHDR", "HDR" */
    public ?string $videoColorRange = null;

    /** e.g. "DolbyAtmos", "Dolby5.1" */
    public ?string $audioTrackFormat = null;
    public bool $hasSubtitles = false;
    public bool $hasClosedCaptions = false;
    public bool $hasAudioDescriptions = false;
}
