<?php

namespace App\Core\Utils;

use App\Core\Contracts\StreamContract;

class Stream implements StreamContract
{
    protected array $items;

    public function __construct(array $items)
    {
        foreach ($items as $item) {
            if (!$item instanceof StreamItem) {
                unset($items[array_search($item, $items)]);
            }
        }

        $this->items = $items;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
