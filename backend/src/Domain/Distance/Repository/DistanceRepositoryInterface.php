<?php

declare(strict_types=1);

namespace App\Domain\Distance\Repository;

use App\Domain\Distance\Distance;

interface DistanceRepositoryInterface
{
    /**
     * @return Distance[]
     */
    public function findAll(): array;

    /**
     * @return Distance[]
     */
    public function findByStation(string $stationShortName): array;
}



