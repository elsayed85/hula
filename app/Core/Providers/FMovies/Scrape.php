<?php

namespace App\Core\Providers\FMovies;

use App\Core\Enums\EmbedSite;
use App\Core\Scraping\CustomCrawler;
use App\Core\Utils\Movie;
use App\Core\Utils\Show;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class Scrape
{
    private CustomCrawler $crawler;

    public function __construct()
    {
        $this->crawler = (new CustomCrawler(baseUrl: Fmovies::BASE))->initBrowser();
    }

    public function getMovieSources(Movie $ctx, $id): array
    {
        $episodeId = $this->extractEpisodeId($id);
        $items = $this->fetchEpisodeItems("/ajax/movie/episodes/{$episodeId}");

        return $this->filterValidItems($items);
    }

    public function getShowSources(Show $ctx, $id): array
    {
        $episodeId = $this->extractEpisodeId($id);
        $seasonId = $this->fetchSeasonId($ctx, $episodeId);

        $episodes = $this->fetchEpisodes($seasonId);
        $episodeId = $this->findEpisodeId($ctx, $episodes);

        $items = $this->fetchEpisodeItems("/ajax/episode/servers/{$episodeId}");

        return $this->filterValidItems($items);
    }

    public function getSourceDetails($id)
    {
        $response = Http::get(Fmovies::BASE . "/ajax/sources/{$id}");
        return $response->json('link') ?? null;
    }

    private function extractEpisodeId($id): string
    {
        $idParts = explode('-', $id);
        return end($idParts);
    }

    private function fetchEpisodeItems(string $url): array
    {
        return $this->crawler->get($url)
            ->filter('.nav-item > a')
            ->each(function (Crawler $node) {
                $embed = str_replace('server ', '', strtolower($node->attr('title')));
                $id = $node->attr('data-linkid') ?? $node->attr('data-id');

                if (empty($embed) || empty($id)) {
                    return null;
                }

                return [
                    'embed' => EmbedSite::fromString($embed),
                    'episodeId' => $id,
                ];
            });
    }

    private function fetchSeasonId(Show $ctx, string $episodeId): ?string
    {
        $seasonsList = $this->crawler->get("/ajax/season/list/{$episodeId}")
            ->filter('.dropdown-item')
            ->each(function (Crawler $node) use ($ctx) {
                return $node->text() === "Season {$ctx->season->number}" ? $node->attr('data-id') : null;
            });

        return array_filter($seasonsList)[0] ?? null;
    }

    private function fetchEpisodes(string $seasonId): array
    {
        return $this->crawler->get("/ajax/season/episodes/{$seasonId}")
            ->filter('.nav-item > a')
            ->each(function (Crawler $node) {
                return [
                    'id' => $node->attr('data-id'),
                    'title' => $node->attr('title'),
                ];
            });
    }

    private function findEpisodeId(Show $ctx, array $episodes): string
    {
        $filteredEpisodes = array_filter($episodes, function ($episode) use ($ctx) {
            return str_starts_with($episode['title'], "Eps {$ctx->episode->number}");
        });

        throw_if(empty($filteredEpisodes), new \Exception('Episode not found'));

        return $filteredEpisodes[0]['id'];
    }

    private function filterValidItems(array $items): array
    {
        return array_filter($items);
    }
}
