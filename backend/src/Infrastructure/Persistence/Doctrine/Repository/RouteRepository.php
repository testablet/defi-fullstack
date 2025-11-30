<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Route\Repository\RouteRepositoryInterface;
use App\Domain\Route\Route;
use App\Infrastructure\Persistence\Doctrine\Entity\RouteEntity;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class RouteRepository implements RouteRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function save(Route $route): void
    {
        $entity = RouteEntity::fromDomain($route);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function findByAnalyticCode(
        string $analyticCode,
        ?DateTimeImmutable $from = null,
        ?DateTimeImmutable $to = null
    ): array {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('r')
            ->from(RouteEntity::class, 'r')
            ->where('r.analyticCode = :code')
            ->setParameter('code', $analyticCode);

        if ($from !== null) {
            $qb->andWhere('r.createdAt >= :from')
                ->setParameter('from', $from);
        }

        if ($to !== null) {
            $qb->andWhere('r.createdAt <= :to')
                ->setParameter('to', $to);
        }

        $entities = $qb->getQuery()->getResult();

        return array_map(fn(RouteEntity $e) => $e->toDomain(), $entities);
    }

    public function findAllByDateRange(
        ?DateTimeImmutable $from = null,
        ?DateTimeImmutable $to = null
    ): array {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('r')
            ->from(RouteEntity::class, 'r');

        if ($from !== null) {
            $qb->andWhere('r.createdAt >= :from')
                ->setParameter('from', $from);
        }

        if ($to !== null) {
            $qb->andWhere('r.createdAt <= :to')
                ->setParameter('to', $to);
        }

        $entities = $qb->getQuery()->getResult();

        return array_map(fn(RouteEntity $e) => $e->toDomain(), $entities);
    }
}



