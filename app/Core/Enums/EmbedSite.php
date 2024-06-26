<?php

namespace App\Core\Enums;

enum EmbedSite: string
{
    case UPCLOUD = 'upcloud';
    case VIDCLOUD = 'vidcloud';
    case UNKNOWN = 'unknown';
    case VOE = 'voe';
    case UPSTREAM = 'upstream';
    case MIXDROP = 'mixdrop';
    case DOODSTREAM = 'doodstream';

    public static function fromString(string $type): self
    {
        $obj = self::tryFrom(strtolower($type));
        return $obj ?? self::UNKNOWN;
    }
}
