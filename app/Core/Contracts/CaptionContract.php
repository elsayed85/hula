<?php

namespace App\Core\Contracts;

interface CaptionContract
{
    public function getFile(): string;

    public function getLabel(): string;

    public function getKind(): string;

    public function isDefault(): bool;
}
