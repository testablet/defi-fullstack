<?php

declare(strict_types=1);

namespace App\Domain\Route;

use DateTimeImmutable;

class Route
{
    public function __construct(
        private readonly string $id,
        private readonly string $fromStationId,
        private readonly string $toStationId,
        private readonly string $analyticCode,
        private readonly float $distanceKm,
        private readonly array $path,
        private readonly DateTimeImmutable $createdAt
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFromStationId(): string
    {
        return $this->fromStationId;
    }

    public function getToStationId(): string
    {
        return $this->toStationId;
    }

    public function getAnalyticCode(): string
    {
        return $this->analyticCode;
    }

    public function getDistanceKm(): float
    {
        return $this->distanceKm;
    }

    /**
     * @return string[]
     */
    public function getPath(): array
    {
        return $this->path;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}



