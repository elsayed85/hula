<?php

namespace App\Core\Providers;

use App\Core\Contracts\ProviderContract;
use App\Core\Utils\Media;
use App\Core\Utils\Movie;
use App\Core\Utils\Show;
use Symfony\Component\BrowserKit\HttpBrowser;

abstract class Provider implements ProviderContract
{
    public const BASE = '';

    public function getConfig(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'rank' => $this->getRank(),
            'is_active' => $this->getIsActive()
        ];
    }

    public function scrape(Media $ctx): array|null
    {
        return match (true) {
            $ctx instanceof Movie => $this->scrapeMovie($ctx),
            $ctx instanceof Show => $this->scrapeShow($ctx),
            default => null
        };
    }

}
