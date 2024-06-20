<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use App\Core\Utils\Episode;
use App\Core\Utils\Movie;
use App\Core\Utils\Season;

$router->get('/', function () use ($router) {

    // $link = (new \App\Core\Hosts\Rabbitstream())->getVideo('https://rabbitstream.net/v2/embed-4/Rl3i8i0ZfDOb?z=');

    $flixhq = new \App\Core\Providers\Flixhq\FlixHQ();
    //
    $movie = new Movie(
        title: 'Mad Max: Fury Road',
        year: 2015,
    );

    $urls = $flixhq->scrapeMovie($movie);
//
//    $show = new \App\Core\Utils\Show(
//        title: 'Breaking Bad',
//        year: 2008,
//        season: new Season(number: 1),
//        episode: new Episode(number: 1),
//    );
//
//    $urls = $flixhq->scrapeShow($show);

    $urls = array_map(function ($url) {
        return $url['handler']->scrape($url['url']);
    }, $urls);

    dd($urls);
});
