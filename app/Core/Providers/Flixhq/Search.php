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

    public function getId(Movie|Show $ctx): string|null
    {
        $items = $this->crawler->get('/search/' . preg_replace('/[^a-z0-9A-Z]/', '-', $ctx->title))
            ->filter('.film_list-wrap > div.flw-item')
            ->each(function (Crawler $node) {
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
            });

        $items = array_filter($items);

        $matchingItem = null;
        foreach ($items as $item) {
            if (!$item) {
                continue;
            }

            if ($ctx instanceof Movie) {
                if (compareMedia($ctx, $item['title'], $item['year'])) {
                    $matchingItem = $item;
                    break;
                }
            } else {
                if (compareTitle($ctx->title, $item['title']) && $ctx->season->number < $item['seasons'] + 1) {
                    $matchingItem = $item;
                    break;
                }
            }
        }

        return $matchingItem['id'] ?? null;
    }
}
