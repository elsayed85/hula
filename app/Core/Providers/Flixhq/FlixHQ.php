<?php

namespace App\Core\Providers\Flixhq;

use App\Core\Hosts\Rabbitstream;
use App\Core\Providers\Provider;
use App\Core\Utils\Movie;
use App\Core\Utils\Show;

class FlixHQ extends Provider
{
    public const BASE = 'https://flixhq.to';

    public function getId(): string
    {
        return 'flixhq';
    }

    public function getName(): string
    {
        return 'FlixHQ';
    }

    public function getRank(): int
    {
        return 1;
    }

    public function getIsActive(): bool
    {
        return true;
    }

    public function scrapeMovie(Movie $ctx)
    {
        $id = (new Search())->getId($ctx);
        throw_if(empty($id), new \Exception('Movie not found'));
        $sources = (new Scrape())->getMovieSources($ctx, $id);
        return $this->getEmbeds($sources);
    }

    public function scrapeShow(Show $ctx)
    {
        $id = (new Search())->getId($ctx);
        throw_if(empty($id), new \Exception('Show not found'));
        $sources = (new Scrape())->getShowSources($ctx, $id);
        return $this->getEmbeds($sources);
    }

    private function getEmbeds($sources)
    {
        $scrape = (new Scrape());
        $embeds = [];
        foreach ($sources as $source) {
            $embed = null;
            if (in_array($source['embed'], ['upcloud', 'vidcloud'])) {
                $url = $scrape->getSourceDetails($source['episodeId']);
                $embed = [
                    'id' => app(Rabbitstream::class),
                    'url' => $url
                ];
            }

            if (!is_null($embed))
                $embeds[] = $embed;
        }

        return $embeds;
    }
}
