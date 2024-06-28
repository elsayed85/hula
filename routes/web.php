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

use App\Core\Providers\ProviderFactory;
use App\Core\Utils\Episode;
use App\Core\Utils\Movie;
use App\Core\Utils\Season;
use App\Core\Utils\Show;

$router->get('/', function () use ($router) {
    $streamItems = (new \App\Core\Providers\FMovies\FMovies())
        ->scrape(
//            new Show(
//                title: 'the mandalorian',
//                year: 2019,
//                season: new Season(
//                    number: 1
//                ),
//                episode: new Episode(
//                    number: 1
//                )
//            )
            new Movie(
                title: 'IF',
                year: 2024
            )
        );

    $watchLinks = array_map(function ($embed) {
        return $embed['handler']->scrape($embed['url']);
    }, $streamItems);
});
