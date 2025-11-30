<?php

declare(strict_types=1);

namespace App\Infrastructure\API\Controller;

use App\Domain\Route\Repository\RouteRepositoryInterface;
use App\Domain\Routing\Service\DijkstraRoutingService;
use DateTimeImmutable;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/routes', name: 'api_routes_')]
#[OA\Tag(name: 'Routing')]
class RouteController extends AbstractController
{
    public function __construct(
        private readonly DijkstraRoutingService $routingService,
        private readonly RouteRepositoryInterface $routeRepository,
        private readonly ValidatorInterface $validator
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Route request',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: RouteRequest::class)
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Trajet calculé',
        content: new OA\JsonContent(
            ref: new Model(type: RouteResponse::class)
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Requête invalide',
        content: new OA\JsonContent(
            ref: new Model(type: ErrorResponse::class)
        )
    )]
    #[OA\Response(
        response: 422,
        description: 'Données non valides',
        content: new OA\JsonContent(
            ref: new Model(type: ErrorResponse::class)
        )
    )]
    public function createRoute(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->errorResponse('Invalid JSON', 400, 'INVALID_JSON');
        }

        $constraints = new Assert\Collection([
            'fromStationId' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
            ],
            'toStationId' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
            ],
            'analyticCode' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
            ],
        ]);

        $violations = $this->validator->validate($data, $constraints);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
            }
            return $this->errorResponse('Validation failed', 422, 'VALIDATION_ERROR', $errors);
        }

        try {
            $result = $this->routingService->calculateShortestPath(
                $data['fromStationId'],
                $data['toStationId']
            );

            $route = new \App\Domain\Route\Route(
                (string) Uuid::v4(),
                $data['fromStationId'],
                $data['toStationId'],
                $data['analyticCode'],
                $result['distance'],
                $result['path'],
                new DateTimeImmutable()
            );

            $this->routeRepository->save($route);

            return $this->json([
                'id' => $route->getId(),
                'fromStationId' => $route->getFromStationId(),
                'toStationId' => $route->getToStationId(),
                'analyticCode' => $route->getAnalyticCode(),
                'distanceKm' => $route->getDistanceKm(),
                'path' => $route->getPath(),
                'createdAt' => $route->getCreatedAt()->format('c'),
            ], Response::HTTP_CREATED);
        } catch (\RuntimeException $e) {
            return $this->errorResponse($e->getMessage(), 422, 'ROUTING_ERROR');
        }
    }

    private function errorResponse(
        string $message,
        int $statusCode,
        string $code = 'ERROR',
        array $details = []
    ): JsonResponse {
        $response = [
            'code' => $code,
            'message' => $message,
        ];

        if (!empty($details)) {
            $response['details'] = $details;
        }

        return $this->json($response, $statusCode);
    }
}

/**
 * @OA\Schema(schema="RouteRequest")
 */
class RouteRequest
{
    #[OA\Property(description: 'Station de départ', example: 'MX')]
    public string $fromStationId;

    #[OA\Property(description: 'Station d\'arrivée', example: 'ZW')]
    public string $toStationId;

    #[OA\Property(description: 'Code analytique associé au trajet', example: 'ANA-123')]
    public string $analyticCode;
}

/**
 * @OA\Schema(schema="Route")
 */
class RouteResponse
{
    #[OA\Property(description: 'ID du trajet')]
    public string $id;

    #[OA\Property(description: 'Station de départ')]
    public string $fromStationId;

    #[OA\Property(description: 'Station d\'arrivée')]
    public string $toStationId;

    #[OA\Property(description: 'Code analytique')]
    public string $analyticCode;

    #[OA\Property(description: 'Distance en km', type: 'number')]
    public float $distanceKm;

    #[OA\Property(description: 'Chemin calculé', type: 'array', items: new OA\Items(type: 'string'))]
    public array $path;

    #[OA\Property(description: 'Date de création', type: 'string', format: 'date-time')]
    public string $createdAt;
}

/**
 * @OA\Schema(schema="Error")
 */
class ErrorResponse
{
    #[OA\Property(description: 'Code d\'erreur applicatif')]
    public string $code;

    #[OA\Property(description: 'Message d\'erreur')]
    public string $message;

    #[OA\Property(description: 'Détails', type: 'array', items: new OA\Items(type: 'string'))]
    public ?array $details = null;
}



