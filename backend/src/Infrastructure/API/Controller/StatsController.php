<?php

declare(strict_types=1);

namespace App\Infrastructure\API\Controller;

use App\Domain\Route\Repository\RouteRepositoryInterface;
use DateTimeImmutable;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stats/distances', name: 'api_stats_')]
#[OA\Tag(name: 'Analytics')]
class StatsController extends AbstractController
{
    public function __construct(
        private readonly RouteRepositoryInterface $routeRepository
    ) {
    }

    #[Route('', name: 'distances', methods: ['GET'])]
    #[OA\Parameter(
        name: 'from',
        in: 'query',
        required: false,
        description: 'Date de début (inclus)',
        schema: new OA\Schema(type: 'string', format: 'date')
    )]
    #[OA\Parameter(
        name: 'to',
        in: 'query',
        required: false,
        description: 'Date de fin (inclus)',
        schema: new OA\Schema(type: 'string', format: 'date')
    )]
    #[OA\Parameter(
        name: 'groupBy',
        in: 'query',
        required: false,
        description: 'Optionnel, groupement additionnel',
        schema: new OA\Schema(type: 'string', enum: ['day', 'month', 'year', 'none'], default: 'none')
    )]
    #[OA\Response(
        response: 200,
        description: 'Agrégations de distances',
        content: new OA\JsonContent(
            ref: new Model(type: AnalyticDistanceListResponse::class)
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Paramètres invalides',
        content: new OA\JsonContent(
            ref: new Model(type: ErrorResponse::class)
        )
    )]
    public function getAnalyticDistances(Request $request): JsonResponse
    {
        $from = $request->query->get('from');
        $to = $request->query->get('to');
        $groupBy = $request->query->get('groupBy', 'none');

        $fromDate = null;
        $toDate = null;

        if ($from !== null) {
            try {
                $fromDate = new DateTimeImmutable($from);
            } catch (\Exception $e) {
                return $this->json([
                    'code' => 'INVALID_DATE',
                    'message' => 'Invalid from date format',
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        if ($to !== null) {
            try {
                $toDate = new DateTimeImmutable($to . ' 23:59:59');
            } catch (\Exception $e) {
                return $this->json([
                    'code' => 'INVALID_DATE',
                    'message' => 'Invalid to date format',
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        if ($fromDate !== null && $toDate !== null && $fromDate > $toDate) {
            return $this->json([
                'code' => 'INVALID_DATE_RANGE',
                'message' => 'from date must be before to date',
            ], Response::HTTP_BAD_REQUEST);
        }

        $routes = $this->routeRepository->findAllByDateRange($fromDate, $toDate);

        $aggregated = [];
        foreach ($routes as $route) {
            $key = $this->getGroupKey($route->getCreatedAt(), $groupBy);
            $analyticCode = $route->getAnalyticCode();

            if (!isset($aggregated[$analyticCode])) {
                $aggregated[$analyticCode] = [];
            }

            if (!isset($aggregated[$analyticCode][$key])) {
                $aggregated[$analyticCode][$key] = 0.0;
            }

            $aggregated[$analyticCode][$key] += $route->getDistanceKm();
        }

        $items = [];
        foreach ($aggregated as $analyticCode => $groups) {
            if ($groupBy === 'none') {
                $total = array_sum($groups);
                $items[] = [
                    'analyticCode' => $analyticCode,
                    'totalDistanceKm' => $total,
                    'periodStart' => $fromDate?->format('Y-m-d'),
                    'periodEnd' => $toDate?->format('Y-m-d'),
                ];
            } else {
                foreach ($groups as $group => $distance) {
                    $items[] = [
                        'analyticCode' => $analyticCode,
                        'totalDistanceKm' => $distance,
                        'periodStart' => $fromDate?->format('Y-m-d'),
                        'periodEnd' => $toDate?->format('Y-m-d'),
                        'group' => $group,
                    ];
                }
            }
        }

        return $this->json([
            'from' => $fromDate?->format('Y-m-d'),
            'to' => $toDate?->format('Y-m-d'),
            'groupBy' => $groupBy,
            'items' => $items,
        ]);
    }

    private function getGroupKey(DateTimeImmutable $date, string $groupBy): string
    {
        return match ($groupBy) {
            'day' => $date->format('Y-m-d'),
            'month' => $date->format('Y-m'),
            'year' => $date->format('Y'),
            default => 'all',
        };
    }
}

/**
 * @OA\Schema(schema="AnalyticDistance")
 */
class AnalyticDistanceResponse
{
    #[OA\Property(description: 'Code analytique')]
    public string $analyticCode;

    #[OA\Property(description: 'Distance totale en km', type: 'number')]
    public float $totalDistanceKm;

    #[OA\Property(description: 'Date de début de période', type: 'string', format: 'date', nullable: true)]
    public ?string $periodStart = null;

    #[OA\Property(description: 'Date de fin de période', type: 'string', format: 'date', nullable: true)]
    public ?string $periodEnd = null;

    #[OA\Property(description: 'Unité de groupement', type: 'string', nullable: true)]
    public ?string $group = null;
}

/**
 * @OA\Schema(schema="AnalyticDistanceList")
 */
class AnalyticDistanceListResponse
{
    #[OA\Property(description: 'Date de début', type: 'string', format: 'date', nullable: true)]
    public ?string $from = null;

    #[OA\Property(description: 'Date de fin', type: 'string', format: 'date', nullable: true)]
    public ?string $to = null;

    #[OA\Property(description: 'Mode de groupement', type: 'string', enum: ['day', 'month', 'year', 'none'])]
    public string $groupBy;

    #[OA\Property(
        description: 'Liste des agrégations',
        type: 'array',
        items: new OA\Items(ref: new Model(type: AnalyticDistanceResponse::class))
    )]
    public array $items;
}


