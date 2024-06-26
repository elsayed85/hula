<?php

namespace App\Core\Providers\Insertunit;

use App\Core\Providers\Provider;
use App\Core\Utils\Movie;
use App\Core\Utils\ProviderResponse;
use App\Core\Utils\Show;

class Insertunit extends Provider
{
    public const BASE = 'https://api.insertunit.ws';

    public static function getId(): string
    {
        return 'insertunit';
    }

    public static function getName(): string
    {
        return 'Insertunit';
    }

    public static function getRank(): int
    {
        return 3;
    }

    public static function getIsActive(): bool
    {
        return true;
    }

    public function scrapeMovie(Movie $ctx): ProviderResponse
    {
        $streams = (new Scrape())->getMovieSources($ctx);
        return (new ProviderResponse(streams: $streams));
    }

    public function scrapeShow(Show $ctx): ProviderResponse
    {
        $streams = (new Scrape())->getShowSources($ctx);
        return (new ProviderResponse(streams: $streams));
    }
}
