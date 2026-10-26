Scraper Apple TV
================

![Packagist version](https://flat.badgen.net/packagist/v/rem42/scraper-apple-tv)
![Packagist license](https://flat.badgen.net/github/license/rem42/scraper-apple-tv)

Scraper for the Apple TV catalog (tv.apple.com): search, movies, shows and their episodes, with cast, directors,
age rating, Rotten Tomatoes scores and the best available quality (4K, Dolby Vision, Dolby Atmos).

It uses the undocumented API of the tv.apple.com web app (`uts-api.itunes.apple.com`). No account is needed,
but the client parameters may change without notice: `AppleTvRequest::setUtsk()` and `setVersion()`
override the default ones.

Limits
------

- The search only covers the Apple TV catalog (Apple Originals and channels). Titles only sold on the
  iTunes Store (e.g. "Inception") are not found: `SearchResult::$isMissingContent` is then `true` and
  the popular titles Apple suggests instead are left out.
- A movie or show page can still be fetched by its identifier (`umc.cmc.…`) for any title.

Installation
------------

````bash
composer require rem42/scraper-apple-tv
````

Requirement
-----------

- PHP >= 8.4
- `rem42/scraper` ^3.4

Usage
-----

````php
use Scraper\Scraper\Client;
use Scraper\ScraperAppleTv\Request\AppleTvMovieRequest;
use Scraper\ScraperAppleTv\Request\AppleTvSearchRequest;
use Scraper\ScraperAppleTv\Request\AppleTvShowEpisodesRequest;
use Scraper\ScraperAppleTv\Request\AppleTvShowRequest;

$client = new Client($httpClient); // any Symfony HttpClientInterface

// Movies and shows, French storefront and texts by default
$search = $client->send((new AppleTvSearchRequest())->setTerm('ted lasso'));
foreach ($search->items as $item) { // Movie|Show
    echo $item->title, ' ', $item->getPoster()?->getUrl(600), PHP_EOL;
}

// Another country and language
$search = $client->send(
    (new AppleTvSearchRequest())
        ->setTerm('ted lasso')
        ->setStorefront(143441) // United States
        ->setLocale('en-US')
);

$movie = $client->send((new AppleTvMovieRequest())->setId('umc.cmc.26o403koqo2klixc0jtqy6tmc'));
echo $movie->duration; // seconds
echo implode(', ', $movie->directors);
echo $movie->rottenTomatoes?->tomatometerPercentage;
echo $movie->badges?->videoQuality; // "UHD"

$show = $client->send((new AppleTvShowRequest())->setId('umc.cmc.vtoh0mn0xn7t3c643xqonfzy'));
echo count($show->seasons);

// Episodes of all seasons, 100 per page
$episodes = $client->send(
    (new AppleTvShowEpisodesRequest())
        ->setId('umc.cmc.vtoh0mn0xn7t3c643xqonfzy')
        ->setPage(0, 100)
);
echo $episodes->totalEpisodeCount;
````

Images
------

Artworks are url templates: `Artwork::getUrl($width)` returns the image at that width, the height following
the original ratio. The main ones are `posterArt` (2:3, also returned by `Content::getPoster()`),
`contentImage16X9` and `contentLogo`.
