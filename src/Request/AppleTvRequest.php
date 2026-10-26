<?php

declare(strict_types=1);

namespace Scraper\ScraperAppleTv\Request;

use Scraper\Scraper\Attribute\Method;
use Scraper\Scraper\Attribute\Scheme;
use Scraper\Scraper\Attribute\Scraper;
use Scraper\Scraper\Request\RequestHeaders;
use Scraper\Scraper\Request\RequestQuery;
use Scraper\Scraper\Request\ScraperRequest;

/**
 * Undocumented API used by the tv.apple.com web app: no account is needed, but the client
 * parameters ("utsk", "v"…) are those served by tv.apple.com and may change without notice.
 */
#[Scraper(method: Method::GET, scheme: Scheme::HTTPS, host: 'uts-api.itunes.apple.com', path: 'uts/v3/')]
abstract class AppleTvRequest extends ScraperRequest implements RequestQuery, RequestHeaders
{
    public const string UTSK = '6e3013c6d6fae3c2::::::235656c069bb0efb';
    public const string VERSION = '100';
    public const int STOREFRONT_FR = 143442;

    /** @var array<string, int|string> */
    protected array $parameters = [
        'caller' => 'web',
        'pfm' => 'web',
        'locale' => 'fr-FR',
        'sf' => self::STOREFRONT_FR,
        'v' => self::VERSION,
        'utscf' => 'OjAAAAAAAAA~',
        'utsk' => self::UTSK,
    ];

    /**
     * @return array<string, int|string>
     */
    public function getQuery(): array
    {
        return $this->parameters;
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36',
            'Origin' => 'https://tv.apple.com',
        ];
    }

    /**
     * Language of the texts, e.g. "fr-FR" or "en-US".
     */
    public function setLocale(string $locale): static
    {
        $this->parameters['locale'] = $locale;

        return $this;
    }

    /**
     * Apple storefront identifier of the country, e.g. 143442 for France or 143441 for the United States.
     */
    public function setStorefront(int $storefront): static
    {
        $this->parameters['sf'] = $storefront;

        return $this;
    }

    /**
     * Overrides the client key when Apple rotates the one served by tv.apple.com.
     */
    public function setUtsk(string $utsk): static
    {
        $this->parameters['utsk'] = $utsk;

        return $this;
    }

    public function setVersion(string $version): static
    {
        $this->parameters['v'] = $version;

        return $this;
    }
}
