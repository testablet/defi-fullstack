<?php

declare(strict_types=1);

namespace App\Domain\Route\Repository;

use App\Domain\Route\Route;
use DateTimeImmutable;

interface RouteRepositoryInterface
{
    public function save(Route $route): void;

    /**
     * @return Route[]
     */
    public function findByAnalyticCode(
        string $analyticCode,
        ?DateTimeImmutable $from = null,
        ?DateTimeImmutable $to = null
    ): array;

    /**
     * @return Route[]
     */
    public function findAllByDateRange(
        ?DateTimeImmutable $from = null,
        ?DateTimeImmutable $to = null
    ): array;
}



