<?php

namespace App\Core\Providers\GoMovies;

use App\Core\Scraping\CustomCrawler;
use App\Core\Utils\Movie;
use App\Core\Utils\Show;
use Symfony\Component\DomCrawler\Crawler;

class Search
{
    private CustomCrawler $crawler;

    public function __construct()
    {
        $this->crawler = (new CustomCrawler(baseUrl: GoMovies::BASE))->initBrowser();
    }

    public function getId(Movie|Show $ctx): ?string
    {
        $items = $this->searchItems($ctx->title);
        $items = array_filter($items);
        return $this->findMatchingItem($ctx, $items);
    }

    private function searchItems(string $title): array
    {
        return $this->crawler
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get('/search/' . preg_replace('/[^a-z0-9A-Z]/', '-', $title))
            ->filter('div.film-detail')
            ->each(function (Crawler $node) {
                return $this->extractItemData($node);
            });
    }

    private function extractItemData(Crawler $node): ?array
    {
        $path = $node->filter('h2.film-name a')->attr('href');
        $name = $node->filter('h2.film-name a')->text(null);
        $year = $node->filter('span.fdi-item')->first()->text(null);

        if (!$name || !$year || !$path) {
            return null;
        }

        return [
            'id' => $path,
            'title' => $name,
            'year' => $year,
        ];
    }

    private function findMatchingItem(Movie|Show $ctx, array $items): ?string
    {
        foreach ($items as $item) {
            if (!$item) {
                continue;
            }

            if ($ctx instanceof Movie) {
                if ($this->isMatchingMovie($ctx, $item)) {
                    return $item['id'];
                }
            } else {
                if ($this->isMatchingShow($ctx, $item)) {
                    return $item['id'];
                }
            }
        }

        return null;
    }

    private function isMatchingMovie(Movie $ctx, array $item): bool
    {
        return compareTitle($ctx->title, $item['title']) && $ctx->year == $item['year'];
    }

    private function isMatchingShow(Show $ctx, array $item): bool
    {
        return compareTitle($ctx->title, $item['title']);
    }
}
