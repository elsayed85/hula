<?php

namespace App\Core\Hosts;

use App\Core\Enums\StreamItemType;
use App\Core\Utils\Caption;
use App\Core\Utils\Embed;
use App\Core\Utils\Stream;
use App\Core\Utils\StreamItem;
use Illuminate\Support\Facades\Http;

class Rabbitstream extends Host
{
    private const EMBED_REGEX = '/embed\-[0-9]+\/([A-z0-9]+)/';
    private const BASE_URL = 'https://aquariumtv.app/rabbit?id=';

    public function scrape(Embed $ctx): Stream
    {
        $id = $this->extractId($ctx->getUrl());
        $content = $this->fetchContent($id);

        $tracks = $this->processTracks($content['tracks'] ?? []);
        $playlist = $this->processPlaylist($content['sources'] ?? [], $tracks);

        return new Stream($playlist);
    }

    private function processTracks(array $tracks): array
    {
        return array_map(function ($track) {
            return new Caption(
                file: $track['file'],
                label: $track['label'],
                kind: $track['kind'],
                default: $track['default'] ?? false,
            );
        }, $tracks);
    }

    private function processPlaylist(array $playlist, array $tracks): array
    {
        return array_map(function ($item) use ($tracks) {
            return new StreamItem(
                type: StreamItemType::fromString($item['type']),
                playlist: $item['file'],
                captions: $tracks,
            );
        }, $playlist);
    }

    private function extractId($url): string
    {
        preg_match(self::EMBED_REGEX, $url, $matches);
        return $matches[1];
    }

    private function fetchContent($id): array
    {
        $response = Http::get(self::BASE_URL . $id);
        return $response->json();
    }


    public static function getId(): string
    {
        return 'rabbitstream';
    }

    public static function getName(): string
    {
        return 'Rabbitstream';
    }

    public static function getRank(): int
    {
        return 1;
    }

    public static function getIsActive(): bool
    {
        return true;
    }
}
