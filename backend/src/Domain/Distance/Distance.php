<?php

declare(strict_types=1);

namespace App\Domain\Distance;

class Distance
{
    public function __construct(
        private readonly string $parentStation,
        private readonly string $childStation,
        private readonly float $distance,
        private readonly string $networkName
    ) {
    }

    public function getParentStation(): string
    {
        return $this->parentStation;
    }

    public function getChildStation(): string
    {
        return $this->childStation;
    }

    public function getDistance(): float
    {
        return $this->distance;
    }

    public function getNetworkName(): string
    {
        return $this->networkName;
    }
}



