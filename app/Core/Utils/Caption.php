<?php

namespace App\Core\Utils;

use App\Core\Contracts\CaptionContract;

class Caption implements CaptionContract
{
    protected string $file;
    protected string $label;
    protected string $kind;
    protected bool $default;

    public function __construct(string $file, string $label, string $kind = 'captions', bool $default = false)
    {
        $this->file = $file;
        $this->label = $label;
        $this->kind = $kind;
        $this->default = $default;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getKind(): string
    {
        return $this->kind;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }
}
