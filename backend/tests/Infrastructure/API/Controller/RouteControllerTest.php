<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\API\Controller;

use App\Domain\Route\Repository\RouteRepositoryInterface;
use App\Domain\Routing\Service\DijkstraRoutingService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RouteControllerTest extends WebTestCase
{
    public function testCreateRouteSuccess(): void
    {
        $client = static::createClient();

        // Mock services
        $routingService = $this->createMock(DijkstraRoutingService::class);
        $routingService->method('calculateShortestPath')
            ->willReturn(['path' => ['MX', 'CGE'], 'distance' => 0.65]);

        $routeRepository = $this->createMock(RouteRepositoryInterface::class);
        $routeRepository->expects($this->once())
            ->method('save');

        static::getContainer()->set(DijkstraRoutingService::class, $routingService);
        static::getContainer()->set(RouteRepositoryInterface::class, $routeRepository);

        $client->request('POST', '/api/v1/routes', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer test-token',
        ], json_encode([
            'fromStationId' => 'MX',
            'toStationId' => 'CGE',
            'analyticCode' => 'ANA-123',
        ]));

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertEquals('MX', $data['fromStationId']);
        $this->assertEquals('CGE', $data['toStationId']);
        $this->assertEquals(0.65, $data['distanceKm']);
    }

    public function testCreateRouteInvalidJson(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/v1/routes', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], 'invalid json');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function testCreateRouteMissingFields(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/v1/routes', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'fromStationId' => 'MX',
        ]));

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $client->getResponse()->getStatusCode());
    }
}



