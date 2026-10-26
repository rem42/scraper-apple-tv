<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Api;

use Scraper\ScraperAppleTv\Model\Episode;
use Scraper\ScraperAppleTv\Model\EpisodeList;

class AppleTvShowEpisodesApi extends AbstractAppleTvApi
{
    public function execute(): object|array|bool|string
    {
        $data = $this->decode();

        $list = new EpisodeList();
        $list->totalEpisodeCount = $this->int($data['totalEpisodeCount'] ?? null);
        $list->nextToken = $this->string($data['nextToken'] ?? null);

        foreach ($this->list($data['episodes'] ?? null) as $item) {
            $episode = new Episode();
            $episode->id = $this->string($item['id'] ?? null);
            $episode->title = $this->string($item['title'] ?? null);
            $episode->description = $this->string($item['description'] ?? null);
            $episode->seasonNumber = $this->int($item['seasonNumber'] ?? null);
            $episode->episodeNumber = $this->int($item['episodeNumber'] ?? null);
            $episode->duration = $this->int($item['duration'] ?? null);
            $episode->releaseDate = $this->date($item['releaseDate'] ?? null);
            $episode->url = $this->string($item['url'] ?? null);
            $list->episodes[] = $episode;
        }

        return $list;
    }
}
