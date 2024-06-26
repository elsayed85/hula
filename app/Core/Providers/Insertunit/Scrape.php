<?php

namespace App\Core\Providers\Insertunit;

use App\Core\Enums\EmbedSite;
use App\Core\Providers\Flixhq\FlixHQ;
use App\Core\Scraping\CustomCrawler;
use App\Core\Scraping\HttpClient;
use App\Core\Utils\Movie;
use App\Core\Utils\Show;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class Scrape
{
    private HttpClient $client;

    public function __construct()
    {
        $this->client = (new HttpClient(baseUrl: Insertunit::BASE))->initClient();
    }

    public function getMovieSources(Movie $ctx): array
    {
        $html = $this->getPlayerData($ctx->imdb_id);
        $regx = '/hls: "([^"]*)/';
        preg_match($regx, $html, $matches);
        $streamData = $matches[1];
        $subtitleRegx = '/cc: (.*)/';
        preg_match($subtitleRegx, $html, $matches);
        $subtitleData = $matches[1];
        $subtitleData = json_decode($subtitleData, true);
        dd($subtitleData);
    }

    public function getShowSources(Show $ctx): array
    {

    }

    public function getPlayerData($imdb_id): string
    {
        $response = $this->client->get("/embed/imdb/{$imdb_id}");
        return $response->body();
    }
}
