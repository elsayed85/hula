<?php

namespace App\Core\Contracts;

interface StreamItemContract
{
    public function getFlags(): array;

    public function getCaptions(): array;

    public function getThumbnailTrack(): ?array;

    public function getHeaders(): ?array;

    public function getPreferredHeaders(): ?array;

    public function getType(): string;

    public function getQualities(): ?array;

    public function getPlaylist(): ?string;
}
