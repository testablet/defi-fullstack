<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use App\Domain\Distance\Distance;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'distances')]
#[ORM\Index(columns: ['parent_station'], name: 'idx_parent_station')]
#[ORM\Index(columns: ['child_station'], name: 'idx_child_station')]
class DistanceEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 10, name: 'parent_station')]
    private string $parentStation;

    #[ORM\Column(type: 'string', length: 10, name: 'child_station')]
    private string $childStation;

    #[ORM\Column(type: 'float', name: 'distance')]
    private float $distance;

    #[ORM\Column(type: 'string', length: 50, name: 'network_name')]
    private string $networkName;

    public function getId(): int
    {
        return $this->id;
    }

    public function getParentStation(): string
    {
        return $this->parentStation;
    }

    public function setParentStation(string $parentStation): void
    {
        $this->parentStation = $parentStation;
    }

    public function getChildStation(): string
    {
        return $this->childStation;
    }

    public function setChildStation(string $childStation): void
    {
        $this->childStation = $childStation;
    }

    public function getDistance(): float
    {
        return $this->distance;
    }

    public function setDistance(float $distance): void
    {
        $this->distance = $distance;
    }

    public function getNetworkName(): string
    {
        return $this->networkName;
    }

    public function setNetworkName(string $networkName): void
    {
        $this->networkName = $networkName;
    }

    public function toDomain(): Distance
    {
        return new Distance(
            $this->parentStation,
            $this->childStation,
            $this->distance,
            $this->networkName
        );
    }
}



