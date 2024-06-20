<?php

namespace App\Core\Providers\Flixhq;

use App\Core\Hosts\Rabbitstream;
use App\Core\Providers\Provider;
use App\Core\Utils\Embed;
use App\Core\Utils\Movie;
use App\Core\Utils\Show;
use Illuminate\Support\Facades\App;

class FlixHQ extends Provider
{
    public const BASE = 'https://flixhq.to';

    public static function getId(): string
    {
        return 'flixhq';
    }

    public static function getName(): string
    {
        return 'FlixHQ';
    }

    public static function getRank(): int
    {
        return 1;
    }

    public static function getIsActive(): bool
    {
        return true;
    }

    public function scrapeMovie(Movie $ctx)
    {
        $id = $this->getContentId($ctx);
        throw_if(empty($id), new \Exception('Movie not found'));
        $sources = $this->fetchSources($ctx, $id, 'getMovieSources');
        return $this->extractEmbeds($sources);
    }

    public function scrapeShow(Show $ctx)
    {
        $id = $this->getContentId($ctx);
        throw_if(empty($id), new \Exception('Show not found'));
        $sources = $this->fetchSources($ctx, $id, 'getShowSources');
        return $this->extractEmbeds($sources);
    }

    private function getContentId($ctx)
    {
        return (new Search())->getId($ctx);
    }

    private function fetchSources($ctx, $id, $method)
    {
        return (new Scrape())->$method($ctx, $id);
    }

    private function extractEmbeds($sources): array
    {
        $scrape = new Scrape();
        $embeds = [];

        foreach ($sources as $source) {
            if ($this->isSupportedEmbed($source['embed'])) {
                $url = $scrape->getSourceDetails($source['episodeId']);
                $embeds[] = $this->createEmbed($url);
            }
        }

        return $embeds;
    }

    private function isSupportedEmbed($embed): bool
    {
        return in_array($embed, ['upcloud', 'vidcloud']);
    }

    private function createEmbed($url): array
    {
        return [
            'handler' => App::make(Rabbitstream::class),
            'url' => new Embed(url: $url)
        ];
    }
}

