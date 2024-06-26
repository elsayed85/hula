<?php

namespace App\Core\Contracts;

use App\Core\Utils\Movie;
use App\Core\Utils\ProviderResponse;
use App\Core\Utils\Show;

interface ProviderContract
{
    public static function getId(): string;

    public static function getName(): string;

    public static function getRank(): int;

    public static function getIsActive(): bool;

    public function scrapeMovie(Movie $ctx): ProviderResponse;

    public function scrapeShow(Show $ctx): ProviderResponse;
}
