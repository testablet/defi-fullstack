<?php

declare(strict_types=1);

namespace App\Domain\Station\Repository;

use App\Domain\Station\Station;

interface StationRepositoryInterface
{
    public function findByShortName(string $shortName): ?Station;

    public function findAll(): array;

    public function findById(int $id): ?Station;
}



