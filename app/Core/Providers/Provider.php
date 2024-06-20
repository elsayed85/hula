<?php

namespace App\Core\Providers;

use App\Core\Contracts\ProviderContract;
use Symfony\Component\BrowserKit\HttpBrowser;

abstract class Provider implements ProviderContract
{
    public const BASE = '';

    public function getConfig()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'rank' => $this->getRank(),
            'is_active' => $this->getIsActive()
        ];
    }
}
