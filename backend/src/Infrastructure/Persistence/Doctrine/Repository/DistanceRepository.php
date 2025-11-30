<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Distance\Distance;
use App\Domain\Distance\Repository\DistanceRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Entity\DistanceEntity;
use Doctrine\ORM\EntityManagerInterface;

class DistanceRepository implements DistanceRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function findAll(): array
    {
        $entities = $this->entityManager
            ->getRepository(DistanceEntity::class)
            ->findAll();

        return array_map(fn(DistanceEntity $e) => $e->toDomain(), $entities);
    }

    public function findByStation(string $stationShortName): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $entities = $qb
            ->select('d')
            ->from(DistanceEntity::class, 'd')
            ->where('d.parentStation = :station OR d.childStation = :station')
            ->setParameter('station', $stationShortName)
            ->getQuery()
            ->getResult();

        return array_map(fn(DistanceEntity $e) => $e->toDomain(), $entities);
    }
}



