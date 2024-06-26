<?php

namespace App\Core\Utils;

use App\Core\Contracts\ProviderResponseContract;
use App\Core\Contracts\StreamContract;

class ProviderResponse implements ProviderResponseContract
{
    private array $stream = [];

    private array $embeds = [];

    public function __construct(array $embeds = [], array $streams = [])
    {
        $this->embeds = $embeds;
        $this->stream = $streams;
    }

    public function get(): array
    {
        return [
            'embeds' => $this->embeds,
            'stream' => $this->stream
        ];
    }

    public function getEmbeds(): array
    {
        return $this->embeds;
    }

    public function getStream(): array
    {
        return $this->stream;
    }
}
