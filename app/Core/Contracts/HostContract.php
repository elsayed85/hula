<?php

namespace App\Core\Contracts;

use App\Core\Utils\Embed;
use App\Core\Utils\Stream;

interface HostContract
{
    public static function getId(): string;

    public static function getName(): string;

    public static function getRank(): int;

    public static function getIsActive(): bool;

    public function scrape(Embed $ctx): Stream;
}
