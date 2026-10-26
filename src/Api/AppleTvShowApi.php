<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Api;

use Scraper\Scraper\Exception\ScraperException;
use Scraper\ScraperAppleTv\Model\Season;
use Scraper\ScraperAppleTv\Model\Show;

class AppleTvShowApi extends AbstractAppleTvApi
{
    public function execute(): object|array|bool|string
    {
        $data = $this->decode();

        if (!\is_array($data['content'] ?? null) || 'Show' !== ($data['content']['type'] ?? null)) {
            throw new ScraperException('Apple TV item is not a show');
        }

        $show = new Show();
        $this->hydrateContent($show, $data['content']);

        if (\is_array($data['badges'] ?? null)) {
            $show->badges = $this->createBadges($data['badges']);
        }

        // Seasons are keyed by their identifier
        foreach ($this->list($data['seasons'] ?? null) as $item) {
            $season = new Season();
            $season->id = $this->string($item['id'] ?? null);
            $season->title = $this->string($item['title'] ?? null);
            $season->seasonNumber = $this->int($item['seasonNumber'] ?? null);
            $season->releaseDate = $this->date($item['releaseDate'] ?? null);
            $season->url = $this->string($item['url'] ?? null);
            $show->seasons[] = $season;
        }

        usort($show->seasons, static fn (Season $a, Season $b): int => ($a->seasonNumber ?? 0) <=> ($b->seasonNumber ?? 0));

        return $show;
    }
}
