<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use App\Domain\Station\Station;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'stations')]
class StationEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 10, unique: true, name: 'short_name')]
    private string $shortName;

    #[ORM\Column(type: 'string', length: 255, name: 'long_name')]
    private string $longName;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): void
    {
        $this->shortName = $shortName;
    }

    public function getLongName(): string
    {
        return $this->longName;
    }

    public function setLongName(string $longName): void
    {
        $this->longName = $longName;
    }

    public function toDomain(): Station
    {
        return new Station(
            $this->id,
            $this->shortName,
            $this->longName
        );
    }
}



