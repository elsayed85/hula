<?php

namespace App\Core\Hosts;

use App\Core\Contracts\HostContract;

abstract class Host implements HostContract
{
    public function getConfig(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'rank' => $this->getRank(),
            'is_active' => $this->getIsActive()
        ];
    }
}
