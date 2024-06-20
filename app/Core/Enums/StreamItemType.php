<?php

namespace App\Core\Enums;

enum StreamItemType: string
{
    case HLS = 'hls';
    case DASH = 'dash';
    case MP4 = 'mp4';
    case WEBM = 'webm';
    case MKV = 'mkv';
    case FLV = 'flv';
    case M3U8 = 'm3u8';
    case MPD = 'mpd';

    case UNKNOWN = 'unknown';

    // from string
    public static function fromString(string $type): self
    {
        $obj = self::tryFrom(strtolower($type));
        return $obj ?? self::UNKNOWN;
    }
}
