<?php

namespace App\Core\Contracts;

use App\Core\Utils\Movie;
use App\Core\Utils\Show;

interface ProviderContract
{
    public function getId() : string;
    public function getName() : string;
    public function getRank() : int;
    public function getIsActive() : bool;
    public function scrapeMovie(Movie $ctx);
    public function scrapeShow(Show $ctx);
}
