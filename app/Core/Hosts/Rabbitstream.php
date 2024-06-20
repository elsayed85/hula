<?php

namespace App\Core\Hosts;

use Illuminate\Support\Facades\Http;

class Rabbitstream extends Host
{
    public static function getVideo($url)
    {
        $re = '/embed\-[0-9]+\/([A-z0-9]+)/';
        preg_match($re, $url, $matches);
        $id = $matches[1];
        $url = 'https://aquariumtv.app/rabbit?id' . $id;
        $content = Http::get($url)->json();
        $tracks = $content['tracks'] ?? [];
        $playlist = $content['sources'] ?? [];
        return [
            'playlist' => $playlist,
            'tracks' => $tracks,
        ];
    }

    public static function getId(): string
    {
        return 'rabbitstream';
    }
}
