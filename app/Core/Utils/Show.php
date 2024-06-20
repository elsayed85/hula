<?php

namespace App\Core\Utils;

class Show extends Media
{
    public string $type = 'show';

    public Season $season;
    public Episode $episode;

    public function __construct($title, $year, Season $season, Episode $episode, $imdb_id = null, $tmdb_id = null)
    {
        parent::__construct($title, $year, $imdb_id, $tmdb_id);
        $this->season = $season;
        $this->episode = $episode;
    }
}
