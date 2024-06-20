<?php

namespace App\Core\Providers\Flixhq;

use App\Core\Scraping\CustomCrawler;
use App\Core\Utils\Movie;
use App\Core\Utils\Show;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class Scrape
{
    private CustomCrawler $crawler;

    public function __construct()
    {
        $this->crawler = (new CustomCrawler(baseUrl: FlixHQ::BASE))->initBrowser();
    }

    public function getMovieSources(Movie $ctx, $id): array
    {
        $id = explode('-', $id);
        $episodeId = end($id);
        $items = $this->crawler->get("/ajax/movie/episodes/{$episodeId}")
            ->filter('.nav-item > a')
            ->each(function (Crawler $node) {
                $embed = $node->attr('title');
                $embed = str_replace('server ', '', strtolower($embed));
                $id = $node->attr('data-linkid');

                if (empty($embed) || empty($id)) {
                    return null;
                }

                return [
                    'embed' => $embed,
                    'episodeId' => $id,
                ];
            });

        return array_filter($items);
    }

    public function getShowSources(Show $ctx, $id): array
    {
        $id = explode('-', $id);
        $episodeId = end($id);
        $seasonsList = $this->crawler->get("/ajax/season/list/{$episodeId}")->filter('.dropdown-item');

        $seasonId = $seasonsList->each(function ($node, $carry) use ($ctx) {
            if ($node->text() === "Season {$ctx->season->number}") {
                return $node->attr('data-id');
            }

            return null;
        });

        $seasonId = array_filter($seasonId)[0] ?? null;

        throw_if(empty($seasonId), new \Exception('Season not found'));

        $episodes = $this->crawler->get("/ajax/season/episodes/{$seasonId}")
            ->filter('.nav-item > a')
            ->each(function (Crawler $node) {
                $id = $node->attr('data-id');
                $title = $node->attr('title');

                return [
                    'id' => $id,
                    'title' => $title,
                ];
            });

        $episodes = array_filter($episodes, function ($episode) use ($ctx) {
            return str_starts_with($episode['title'], "Eps {$ctx->episode->number}");
        });

        throw_if(empty($episodes), new \Exception('Episode not found'));

        $episodeId = $episodes[0]['id'];

        $items = $this->crawler->get("/ajax/episode/servers/{$episodeId}")
            ->filter('.nav-item > a')
            ->each(function (Crawler $node) {
                $embed = $node->attr('title');
                $embed = str_replace('server ', '', strtolower($embed));
                $id = $node->attr('data-id');

                if (empty($embed) || empty($id)) {
                    return null;
                }

                return [
                    'embed' => $embed,
                    'episodeId' => $id,
                ];
            });

        return array_filter($items);
    }

    public function getSourceDetails($id)
    {
        $response = Http::get(FlixHQ::BASE . "/ajax/sources/{$id}");
        return $response->json('link') ?? null;
    }
}
