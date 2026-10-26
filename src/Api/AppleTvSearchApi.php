<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Api;

use Scraper\ScraperAppleTv\Model\SearchResult;

class AppleTvSearchApi extends AbstractAppleTvApi
{
    public function execute(): object|array|bool|string
    {
        $data = $this->decode();

        $result = new SearchResult();
        $result->term = $this->string($data['searchTerm'] ?? null);
        $result->isMissingContent = true === ($data['isMissingContent'] ?? false);

        // Nothing matched: Apple suggests popular titles instead, they are not results
        if ($result->isMissingContent) {
            return $result;
        }

        $canvas = \is_array($data['canvas'] ?? null) ? $data['canvas'] : [];

        $items = [];

        // The same title is listed in several shelves ("Top results", "Movies", "TV shows")
        foreach ($this->list($canvas['shelves'] ?? null) as $shelf) {
            foreach ($this->list($shelf['items'] ?? null) as $item) {
                $content = $this->createContent($item);

                if (null !== $content && null !== $content->id && !isset($items[$content->id])) {
                    $items[$content->id] = $content;
                }
            }
        }

        $result->items = array_values($items);

        return $result;
    }
}
