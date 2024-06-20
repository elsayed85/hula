<?php

namespace App\Core\Providers\Flixhq;

use App\Core\Scraping\CustomCrawler;
use App\Core\Utils\Movie;
use App\Core\Utils\Show;
use Symfony\Component\DomCrawler\Crawler;

class Search
{
    private CustomCrawler $crawler;

    public function __construct()
    {
        $this->crawler = (new CustomCrawler(baseUrl: FlixHQ::BASE))->initBrowser();
    }

    public function getId(Movie|Show $ctx): ?string
    {
        $items = $this->searchItems($ctx->title);
        $items = array_filter($items);

        return $this->findMatchingItem($ctx, $items);
    }

    private function searchItems(string $title): array
    {
        return $this->crawler->get('/search/' . preg_replace('/[^a-z0-9A-Z]/', '-', $title))
            ->filter('.film_list-wrap > div.flw-item')
            ->each(function (Crawler $node) {
                return $this->extractItemData($node);
            });
    }

    private function extractItemData(Crawler $node): ?array
    {
        $id = $node->filter('div.film-poster > a')->attr('href');
        $title = $node->filter('div.film-detail > h2 > a')->attr('title');
        $year = $node->filter('div.film-detail > div.fd-infor > span:nth-child(1)')->text();
        $seasons = str_contains($year, 'SS') ? explode('SS', $year)[1] : '0';

        if (empty($id) || empty($title) || empty($year)) {
            return null;
        }

        return [
            'id' => $id,
            'title' => $title,
            'year' => (int)$year,
            'seasons' => (int)$seasons,
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
        return compareTitle($ctx->title, $item['title']) && $ctx->season->number <= $item['seasons'];
    }
}
