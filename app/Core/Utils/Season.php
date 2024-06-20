<?php

namespace App\Core\Utils;

final class Season
{
    public int $number;
    public ?string $tmdb_id;

    public function __construct(int $number, ?string $tmdb_id = null)
    {
        $this->number = $number;
        $this->tmdb_id = $tmdb_id;
    }
}
