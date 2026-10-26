<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Api;

use Scraper\Scraper\Api\AbstractApi;
use Scraper\Scraper\Exception\ScraperException;
use Scraper\ScraperAppleTv\Model\Artwork;
use Scraper\ScraperAppleTv\Model\Badges;
use Scraper\ScraperAppleTv\Model\Content;
use Scraper\ScraperAppleTv\Model\ContentRating;
use Scraper\ScraperAppleTv\Model\Country;
use Scraper\ScraperAppleTv\Model\Genre;
use Scraper\ScraperAppleTv\Model\Movie;
use Scraper\ScraperAppleTv\Model\RottenTomatoes;
use Scraper\ScraperAppleTv\Model\Show;

/**
 * The responses are deeply nested and loosely typed: the models are hydrated by hand
 * instead of using the serializer.
 */
abstract class AbstractAppleTvApi extends AbstractApi
{
    /**
     * @return array<mixed>
     *
     * @throws ScraperException
     */
    protected function decode(): array
    {
        $content = json_decode($this->response->getContent(), true, 512, \JSON_THROW_ON_ERROR);

        if (!\is_array($content) || !\is_array($content['data'] ?? null)) {
            throw new ScraperException('Apple TV response has no "data" object');
        }

        return $content['data'];
    }

    /**
     * @param array<mixed> $item
     */
    protected function hydrateContent(Content $content, array $item): void
    {
        $content->id = $this->string($item['id'] ?? null);
        $content->title = $this->string($item['title'] ?? null);
        $content->url = $this->string($item['url'] ?? null);
        $content->description = $this->string($item['description'] ?? null);
        $content->releaseDate = $this->date($item['releaseDate'] ?? null);
        $content->isAppleOriginal = true === ($item['isAppleOriginal'] ?? false);
        $content->studio = $this->string($item['studio'] ?? null);
        $content->copyright = $this->string($item['copyright'] ?? null);

        foreach ($this->list($item['genres'] ?? null) as $data) {
            $genre = new Genre();
            $genre->id = $this->string($data['id'] ?? null);
            $genre->name = $this->string($data['name'] ?? null);
            $content->genres[] = $genre;
        }

        foreach (\is_array($item['images'] ?? null) ? $item['images'] : [] as $role => $data) {
            if (\is_string($role) && \is_array($data)) {
                $artwork = new Artwork();
                $artwork->url = $this->string($data['url'] ?? null);
                $artwork->width = $this->int($data['width'] ?? null);
                $artwork->height = $this->int($data['height'] ?? null);
                $content->images[$role] = $artwork;
            }
        }

        foreach ($this->list($item['countriesOfOrigin'] ?? null) as $data) {
            $country = new Country();
            $country->code = $this->string($data['countryCode'] ?? null);
            $country->name = $this->string($data['displayName'] ?? null);
            $content->countries[] = $country;
        }

        $content->languages = $this->strings(array_map(static fn (array $language) => $language['displayName'] ?? null, $this->list($item['originalSpokenLanguages'] ?? null)));
        $content->advisories = $this->strings(array_map(static fn (array $advisory) => $advisory['description'] ?? null, $this->list($item['contentAdvisoryDetails'] ?? null)));

        $roles = \is_array($item['rolesSummary'] ?? null) ? $item['rolesSummary'] : [];
        $content->cast = $this->strings($roles['cast'] ?? null);

        if ($content instanceof Movie) {
            $content->duration = $this->int($item['duration'] ?? null);
            $content->directors = $this->strings($roles['directors'] ?? null);
        }

        if (\is_array($item['rating'] ?? null)) {
            $rating = new ContentRating();
            $rating->name = $this->string($item['rating']['name'] ?? null);
            $rating->displayName = $this->string($item['rating']['displayName'] ?? null);
            $rating->system = $this->string($item['rating']['system'] ?? null);
            $rating->value = $this->int($item['rating']['value'] ?? null);
            $content->rating = $rating;
        }

        if (\is_array($item['rottenTomatoesReviews'] ?? null)) {
            $data = $item['rottenTomatoesReviews'];
            $reviews = new RottenTomatoes();
            $reviews->tomatometerPercentage = $this->int($data['tomatometerPercentage'] ?? null);
            $reviews->tomatometerFreshness = $this->string($data['tomatometerFreshness'] ?? null);
            $reviews->audienceScore = $this->int($data['audienceScore'] ?? null);
            $reviews->averageRating = $this->float($data['averageRating'] ?? null);
            $reviews->numFreshReviews = $this->int($data['numFreshReviews'] ?? null);
            $reviews->numRottenReviews = $this->int($data['numRottenReviews'] ?? null);
            $reviews->consensus = $this->string($data['consensus'] ?? null);
            $reviews->url = $this->string($data['url'] ?? null);
            $content->rottenTomatoes = $reviews;
        }
    }

    /**
     * @param array<mixed> $item
     */
    protected function createContent(array $item): Movie|Show|null
    {
        $content = match ($item['type'] ?? null) {
            'Movie' => new Movie(),
            'Show' => new Show(),
            default => null,
        };

        if (null !== $content) {
            $this->hydrateContent($content, $item);
        }

        return $content;
    }

    /**
     * @param array<mixed> $data
     */
    protected function createBadges(array $data): Badges
    {
        $badges = new Badges();
        $badges->videoQuality = $this->string($data['videoQuality'] ?? null);
        $badges->videoColorRange = $this->string($data['videoColorRange'] ?? null);
        $badges->audioTrackFormat = $this->string($data['audioTrackFormat'] ?? null);
        $badges->hasSubtitles = true === ($data['hasSubtitles'] ?? false);
        $badges->hasClosedCaptions = true === ($data['hasClosedCaptions'] ?? false);
        $badges->hasAudioDescriptions = true === ($data['hasAudioDescriptions'] ?? false);

        return $badges;
    }

    protected function string(mixed $value): ?string
    {
        return \is_string($value) && '' !== $value ? $value : null;
    }

    protected function int(mixed $value): ?int
    {
        return match (true) {
            \is_int($value) => $value,
            \is_float($value), \is_string($value) && is_numeric($value) => (int) $value,
            default => null,
        };
    }

    protected function float(mixed $value): ?float
    {
        return \is_int($value) || \is_float($value) ? (float) $value : null;
    }

    /**
     * Apple dates are timestamps in milliseconds.
     */
    protected function date(mixed $value): ?\DateTimeImmutable
    {
        $milliseconds = $this->int($value);

        if (null === $milliseconds) {
            return null;
        }

        return new \DateTimeImmutable('@' . intdiv($milliseconds, 1000));
    }

    /**
     * @return array<int, array<mixed>>
     */
    protected function list(mixed $value): array
    {
        return \is_array($value) ? array_values(array_filter($value, 'is_array')) : [];
    }

    /**
     * @return array<int, string>
     */
    protected function strings(mixed $value): array
    {
        return \is_array($value) ? array_values(array_filter($value, static fn (mixed $item): bool => \is_string($item) && '' !== $item)) : [];
    }
}
