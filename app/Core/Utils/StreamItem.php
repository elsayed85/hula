<?php

namespace App\Core\Utils;

use App\Core\Contracts\StreamItemContract;
use App\Core\Enums\StreamItemType;

class StreamItem implements StreamItemContract
{
    protected string|StreamItemType $type;
    protected array $flags;
    protected array $captions;
    protected ?array $thumbnailTrack;
    protected ?array $headers;
    protected ?array $preferredHeaders;
    protected ?array $qualities;
    protected ?string $playlist;

    public function __construct(
        string|StreamItemType $type,
        ?string               $playlist = null,
        ?array                $captions = [],
        ?array                $flags = [],
        ?array                $thumbnailTrack = [],
        ?array                $headers = [],
        ?array                $preferredHeaders = [],
        ?array                $qualities = [],
    )
    {
        $this->type = $type;
        $this->flags = $flags;
        $this->captions = $captions;
        $this->thumbnailTrack = $thumbnailTrack;
        $this->headers = $headers;
        $this->preferredHeaders = $preferredHeaders;
        $this->qualities = $qualities;
        $this->playlist = $playlist;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getFlags(): array
    {
        return $this->flags;
    }

    public function getCaptions(): array
    {
        return $this->captions;
    }

    public function getThumbnailTrack(): ?array
    {
        return $this->thumbnailTrack;
    }

    public function getHeaders(): ?array
    {
        return $this->headers;
    }

    public function getPreferredHeaders(): ?array
    {
        return $this->preferredHeaders;
    }

    public function getQualities(): ?array
    {
        return $this->qualities;
    }

    public function getPlaylist(): ?string
    {
        return $this->playlist;
    }
}
