<?php

declare(strict_types=1);

namespace App\Domain\Station;

class Station
{
    public function __construct(
        private readonly int $id,
        private readonly string $shortName,
        private readonly string $longName
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getLongName(): string
    {
        return $this->longName;
    }
}



