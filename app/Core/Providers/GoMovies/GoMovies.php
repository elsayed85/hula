<?php

namespace App\Core\Providers\GoMovies;

use App\Core\Enums\EmbedSite;
use App\Core\Hosts\Rabbitstream;
use App\Core\Providers\Provider;
use App\Core\Utils\Embed;
use App\Core\Utils\Movie;
use App\Core\Utils\Show;

class   GoMovies extends Provider
{
    public const BASE = 'https://gomovies.sx';

    private const SUPPORTED_EMBEDS = [EmbedSite::UPCLOUD, EmbedSite::VIDCLOUD];

    public static function getId(): string
    {
        return 'gomovies';
    }

    public static function getName(): string
    {
        return 'GoMovies';
    }

    public static function getRank(): int
    {
        return 2;
    }

    public static function getIsActive(): bool
    {
        return true;
    }

    public function scrapeMovie(Movie $ctx): array
    {
        $id = $this->getContentId($ctx);
        throw_if(empty($id), new \Exception('Movie not found'));
        $sources = $this->fetchSources($ctx, $id, 'getMovieSources');
        return $this->extractEmbeds($sources);
    }

    public function scrapeShow(Show $ctx): array
    {
        $id = $this->getContentId($ctx);
        throw_if(empty($id), new \Exception('Show not found'));
        $sources = $this->fetchSources($ctx, $id, 'getShowSources');
        return $this->extractEmbeds($sources);
    }

    private function getContentId($ctx): string
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
            if ($this->isSupportedRabbitstreamEmbed($source['embed'])) {
                $url = $scrape->getSourceDetails($source['episodeId']);
                $embeds[] = $this->createRabbitstreamEmbed($url);
            }
        }

        return $embeds;
    }

    private function isSupportedRabbitstreamEmbed($embed): bool
    {
        return in_array($embed, self::SUPPORTED_EMBEDS);
    }

    private function createRabbitstreamEmbed($url): array
    {
        return [
            'handler' => app()->make(Rabbitstream::class),
            'url' => new Embed(url: $url)
        ];
    }
}

