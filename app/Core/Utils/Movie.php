<?php

namespace App\Core\Utils;

class Movie extends Media
{
    public string $type = 'movie';

    public function __construct($title, $year, $imdb_id = null, $tmdb_id = null)
    {
        parent::__construct($title, $year, $imdb_id, $tmdb_id);
    }
}
