<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use App\Domain\Route\Route;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'routes')]
#[ORM\Index(columns: ['analytic_code'], name: 'idx_analytic_code')]
#[ORM\Index(columns: ['created_at'], name: 'idx_created_at')]
class RouteEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 10, name: 'from_station_id')]
    private string $fromStationId;

    #[ORM\Column(type: 'string', length: 10, name: 'to_station_id')]
    private string $toStationId;

    #[ORM\Column(type: 'string', length: 100, name: 'analytic_code')]
    private string $analyticCode;

    #[ORM\Column(type: 'float', name: 'distance_km')]
    private float $distanceKm;

    #[ORM\Column(type: 'json', name: 'path')]
    private array $path;

    #[ORM\Column(type: 'datetime_immutable', name: 'created_at')]
    private DateTimeImmutable $createdAt;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getFromStationId(): string
    {
        return $this->fromStationId;
    }

    public function setFromStationId(string $fromStationId): void
    {
        $this->fromStationId = $fromStationId;
    }

    public function getToStationId(): string
    {
        return $this->toStationId;
    }

    public function setToStationId(string $toStationId): void
    {
        $this->toStationId = $toStationId;
    }

    public function getAnalyticCode(): string
    {
        return $this->analyticCode;
    }

    public function setAnalyticCode(string $analyticCode): void
    {
        $this->analyticCode = $analyticCode;
    }

    public function getDistanceKm(): float
    {
        return $this->distanceKm;
    }

    public function setDistanceKm(float $distanceKm): void
    {
        $this->distanceKm = $distanceKm;
    }

    /**
     * @return string[]
     */
    public function getPath(): array
    {
        return $this->path;
    }

    /**
     * @param string[] $path
     */
    public function setPath(array $path): void
    {
        $this->path = $path;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function toDomain(): Route
    {
        return new Route(
            $this->id,
            $this->fromStationId,
            $this->toStationId,
            $this->analyticCode,
            $this->distanceKm,
            $this->path,
            $this->createdAt
        );
    }

    public static function fromDomain(Route $route): self
    {
        $entity = new self();
        $entity->setId($route->getId());
        $entity->setFromStationId($route->getFromStationId());
        $entity->setToStationId($route->getToStationId());
        $entity->setAnalyticCode($route->getAnalyticCode());
        $entity->setDistanceKm($route->getDistanceKm());
        $entity->setPath($route->getPath());
        $entity->setCreatedAt($route->getCreatedAt());

        return $entity;
    }
}



