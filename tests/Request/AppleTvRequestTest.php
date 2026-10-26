<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Tests\Request;

use Scraper\ScraperAppleTv\Model\EpisodeList;
use Scraper\ScraperAppleTv\Model\Movie;
use Scraper\ScraperAppleTv\Model\SearchResult;
use Scraper\ScraperAppleTv\Model\Show;
use Scraper\ScraperAppleTv\Request\AppleTvMovieRequest;
use Scraper\ScraperAppleTv\Request\AppleTvRequest;
use Scraper\ScraperAppleTv\Request\AppleTvSearchRequest;
use Scraper\ScraperAppleTv\Request\AppleTvShowEpisodesRequest;
use Scraper\ScraperAppleTv\Request\AppleTvShowRequest;

/**
 * @internal
 */
class AppleTvRequestTest extends AbstractRequestTestCase
{
    public function testDefaultQuery(): void
    {
        $request = (new AppleTvSearchRequest())->setTerm('severance');

        $this->assertSame([
            'caller' => 'web',
            'pfm' => 'web',
            'locale' => 'fr-FR',
            'sf' => AppleTvRequest::STOREFRONT_FR,
            'v' => AppleTvRequest::VERSION,
            'utscf' => 'OjAAAAAAAAA~',
            'utsk' => AppleTvRequest::UTSK,
            'searchTerm' => 'severance',
        ], $request->getQuery());

        $request->setLocale('en-US')->setStorefront(143441)->setUtsk('key')->setVersion('101');
        $this->assertSame('en-US', $request->getQuery()['locale']);
        $this->assertSame(143441, $request->getQuery()['sf']);
        $this->assertSame('key', $request->getQuery()['utsk']);
        $this->assertSame('101', $request->getQuery()['v']);
    }

    public function testSearchKeepsEachTitleOnce(): void
    {
        $result = $this->getClient('search.json')->send((new AppleTvSearchRequest())->setTerm('severance'));

        $this->assertInstanceOf(SearchResult::class, $result);
        $this->assertFalse($result->isMissingContent);
        $this->assertSame('severance', $result->term);
        $this->assertCount(3, $result->items);

        $show = $result->items[0];
        $this->assertInstanceOf(Show::class, $show);
        $this->assertSame('umc.cmc.1srk2goyh2q2zdxcx605w8vtx', $show->id);
        $this->assertSame('Severance', $show->title);
        $this->assertTrue($show->isAppleOriginal);
        $this->assertSame('Thriller', $show->genres[0]->name);
        $this->assertSame('2022-02-18', $show->releaseDate?->format('Y-m-d'));
        $this->assertSame(
            'https://is1-ssl.mzstatic.com/image/thumb/4OgSLb2-5qZ_yXAUvc8Kug/400x600CA.TVA23C01.jpg',
            $show->getPoster()?->getUrl(400)
        );
    }

    public function testSearchWithoutMatchReturnsNoItem(): void
    {
        $result = $this->getClient('search_missing.json')->send((new AppleTvSearchRequest())->setTerm('inception'));

        $this->assertInstanceOf(SearchResult::class, $result);
        $this->assertTrue($result->isMissingContent);
        $this->assertSame([], $result->items);
    }

    public function testMovie(): void
    {
        $request = (new AppleTvMovieRequest())->setId('umc.cmc.6loas01ow0w4lkatxxloz7a6e');
        $this->assertSame('umc.cmc.6loas01ow0w4lkatxxloz7a6e', $request->getId());

        $movie = $this->getClient('movie.json')->send($request);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertSame('Inception', $movie->title);
        $this->assertSame(8880, $movie->duration);
        $this->assertSame(['Christopher Nolan'], $movie->directors);
        $this->assertSame(['Leonardo DiCaprio', 'Joseph Gordon-Levitt', 'Tom Hardy'], $movie->cast);
        $this->assertSame('US', $movie->countries[0]->code);
        $this->assertSame('États-Unis', $movie->countries[0]->name);
        $this->assertSame('Tout public', $movie->rating?->displayName);
        $this->assertSame('Warner Bros.', $movie->studio);
        $this->assertSame(86, $movie->rottenTomatoes?->tomatometerPercentage);
        $this->assertSame(8.4, $movie->rottenTomatoes->averageRating);
        $this->assertSame('UHD', $movie->badges?->videoQuality);
        $this->assertSame('2010-07-21', $movie->releaseDate?->format('Y-m-d'));
        $this->assertNotNull($movie->getPoster()?->url);
    }

    public function testShow(): void
    {
        $show = $this->getClient('show.json')->send((new AppleTvShowRequest())->setId('umc.cmc.1srk2goyh2q2zdxcx605w8vtx'));

        $this->assertInstanceOf(Show::class, $show);
        $this->assertSame('Severance', $show->title);
        $this->assertSame(['Adam Scott', 'Britt Lower', 'Patricia Arquette'], $show->cast);
        $this->assertSame('-12', $show->rating?->displayName);
        $this->assertContains('Violence', $show->advisories);
        $this->assertSame(['Anglais'], $show->languages);
        $this->assertCount(2, $show->seasons);
        $this->assertSame(1, $show->seasons[0]->seasonNumber);
        $this->assertSame(2, $show->seasons[1]->seasonNumber);
        $this->assertSame('DolbyAtmos', $show->badges?->audioTrackFormat);
    }

    public function testShowEpisodes(): void
    {
        $request = (new AppleTvShowEpisodesRequest())->setId('umc.cmc.1srk2goyh2q2zdxcx605w8vtx')->setPage(0);
        $this->assertSame('0:100', $request->getQuery()['nextToken']);

        $list = $this->getClient('episodes.json')->send($request);

        $this->assertInstanceOf(EpisodeList::class, $list);
        $this->assertSame(19, $list->totalEpisodeCount);
        $this->assertCount(19, $list->episodes);
        $this->assertSame(3420, $list->episodes[0]->duration);
        $this->assertSame(2, $list->episodes[18]->seasonNumber);
        $this->assertSame(10, $list->episodes[18]->episodeNumber);
        $this->assertNull($list->nextToken);
    }

    public function testEpisodesPageCountIsChecked(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new AppleTvShowEpisodesRequest())->setPage(0, 101);
    }
}
