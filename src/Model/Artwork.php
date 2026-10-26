<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Model;

/**
 * Image served at the requested size: the url is a template with {w}, {h} and {f} placeholders.
 */
class Artwork
{
    public ?string $url = null;
    public ?int $width = null;
    public ?int $height = null;

    /**
     * Url of the image at the given width, the height following the original ratio when it is not given.
     */
    public function getUrl(?int $width = null, ?int $height = null, string $format = 'jpg'): ?string
    {
        if (null === $this->url) {
            return null;
        }

        $width ??= $this->width ?? 0;

        if (null === $height) {
            $height = null !== $this->width && null !== $this->height && $this->width > 0
                ? (int) round($width * $this->height / $this->width)
                : $width;
        }

        return str_replace(['{w}', '{h}', '{f}'], [(string) $width, (string) $height, $format], $this->url);
    }
}
