<?php

namespace App\Core\Utils;

class Media
{
    public string $type;
    public string $title;
    public int $year;
    public ?string $imdb_id;
    public ?string $tmdb_id;

    public function __construct($title , $year , $imdb_id = null, $tmdb_id = null)
    {
        $this->title = $title;
        $this->year = $year;
        $this->imdb_id = $imdb_id;
        $this->tmdb_id = $tmdb_id;
    }
}
