<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Model;

/**
 * Fields shared by movies and shows. Search results only fill the main ones (title, genres, images…).
 */
abstract class Content
{
    public ?string $id = null;
    public ?string $title = null;
    public ?string $url = null;
    public ?string $description = null;
    public ?\DateTimeImmutable $releaseDate = null;
    public bool $isAppleOriginal = false;

    /** @var array<int, Genre> */
    public array $genres = [];

    /**
     * Artworks by role, e.g. "posterArt" (2:3), "contentImage16X9", "contentLogo", "shelfItemImage".
     *
     * @var array<string, Artwork>
     */
    public array $images = [];

    /** @var array<int, Country> */
    public array $countries = [];

    /**
     * Original languages, named in the requested locale.
     *
     * @var array<int, string>
     */
    public array $languages = [];

    /**
     * Main actors.
     *
     * @var array<int, string>
     */
    public array $cast = [];

    /**
     * Content advisories, e.g. "Violence".
     *
     * @var array<int, string>
     */
    public array $advisories = [];

    public ?ContentRating $rating = null;
    public ?string $studio = null;
    public ?string $copyright = null;
    public ?RottenTomatoes $rottenTomatoes = null;
    public ?Badges $badges = null;

    public function getPoster(): ?Artwork
    {
        return $this->images['posterArt'] ?? $this->images['shelfItemImage'] ?? $this->images['contentImage'] ?? null;
    }
}
