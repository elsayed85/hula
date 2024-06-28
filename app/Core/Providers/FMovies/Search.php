<?php

namespace App\Core\Providers\FMovies;

use App\Core\Scraping\CustomCrawler;
use App\Core\Utils\Movie;
use App\Core\Utils\Show;
use Symfony\Component\DomCrawler\Crawler;

class Search
{
    private CustomCrawler $crawler;

    public function __construct()
    {
        $this->crawler = (new CustomCrawler(baseUrl: FMovies::BASE))->initBrowser();
    }

    public function getId(Movie|Show $ctx): ?string
    {
        $items = $this->searchItems($ctx);
        $items = array_filter($items);
        return $this->findMatchingItem($ctx, $items);
    }

    private function searchItems(Movie|Show $ctx): array
    {
        $title = str_replace("", "+", $ctx->title);
        $type = $ctx instanceof Movie ? 'movie' : 'tv';
        $year = $ctx->year;
        return $this->crawler->get('/filter?keyword=' . $title . '&type[]=' . $type . '&year[]=' . $year)
            ->filter('.movies.items')
            ->each(function (Crawler $node) {
                return $this->extractItemData($node);
            });
    }

    private function extractItemData(Crawler $node): ?array
    {
        $id = $node->filter('a')->attr('data-tip');
        $id = explode('?', $id)[0];
        $title = $node->filter('.meta > a')->text('');
        $year = $node->filter('.meta > div > span')->text('');
        $seasons = $node->filter('.meta span.type')->text('');
        $seasons = preg_replace('/\D/', '', $seasons);

        if (empty($id) || empty($title) || empty($year)) {
            return null;
        }

        return [
            'id' => (int)$id,
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
