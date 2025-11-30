<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Station\Repository\StationRepositoryInterface;
use App\Domain\Station\Station;
use App\Infrastructure\Persistence\Doctrine\Entity\StationEntity;
use Doctrine\ORM\EntityManagerInterface;

class StationRepository implements StationRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function findByShortName(string $shortName): ?Station
    {
        $entity = $this->entityManager
            ->getRepository(StationEntity::class)
            ->findOneBy(['shortName' => $shortName]);

        return $entity?->toDomain();
    }

    public function findAll(): array
    {
        $entities = $this->entityManager
            ->getRepository(StationEntity::class)
            ->findAll();

        return array_map(fn(StationEntity $e) => $e->toDomain(), $entities);
    }

    public function findById(int $id): ?Station
    {
        $entity = $this->entityManager
            ->getRepository(StationEntity::class)
            ->find($id);

        return $entity?->toDomain();
    }
}



