<?php

declare(strict_types=1);

namespace App\Tests\Domain\Routing\Service;

use App\Domain\Distance\Distance;
use App\Domain\Distance\Repository\DistanceRepositoryInterface;
use App\Domain\Routing\Service\DijkstraRoutingService;
use App\Domain\Station\Repository\StationRepositoryInterface;
use App\Domain\Station\Station;
use PHPUnit\Framework\TestCase;

class DijkstraRoutingServiceTest extends TestCase
{
    private StationRepositoryInterface $stationRepository;
    private DistanceRepositoryInterface $distanceRepository;
    private DijkstraRoutingService $service;

    protected function setUp(): void
    {
        $this->stationRepository = $this->createMock(StationRepositoryInterface::class);
        $this->distanceRepository = $this->createMock(DistanceRepositoryInterface::class);
        $this->service = new DijkstraRoutingService(
            $this->stationRepository,
            $this->distanceRepository
        );
    }

    public function testCalculateShortestPathSameStation(): void
    {
        $station = new Station(1, 'MX', 'Montreux');
        $this->stationRepository
            ->method('findByShortName')
            ->willReturn($station);

        $result = $this->service->calculateShortestPath('MX', 'MX');

        $this->assertEquals(['path' => ['MX'], 'distance' => 0.0], $result);
    }

    public function testCalculateShortestPathDirectConnection(): void
    {
        $station1 = new Station(1, 'MX', 'Montreux');
        $station2 = new Station(2, 'CGE', 'Montreux-Collège');

        $this->stationRepository
            ->method('findByShortName')
            ->willReturnCallback(fn($name) => match($name) {
                'MX' => $station1,
                'CGE' => $station2,
            });

        $distance = new Distance('MX', 'CGE', 0.65, 'MOB');
        $this->distanceRepository
            ->method('findAll')
            ->willReturn([$distance]);

        $result = $this->service->calculateShortestPath('MX', 'CGE');

        $this->assertEquals(['path' => ['MX', 'CGE'], 'distance' => 0.65], $result);
    }

    public function testCalculateShortestPathMultiHop(): void
    {
        $stations = [
            'MX' => new Station(1, 'MX', 'Montreux'),
            'CGE' => new Station(2, 'CGE', 'Montreux-Collège'),
            'VUAR' => new Station(3, 'VUAR', 'Vuarennes'),
        ];

        $this->stationRepository
            ->method('findByShortName')
            ->willReturnCallback(fn($name) => $stations[$name] ?? null);

        $distances = [
            new Distance('MX', 'CGE', 0.65, 'MOB'),
            new Distance('CGE', 'VUAR', 0.35, 'MOB'),
        ];

        $this->distanceRepository
            ->method('findAll')
            ->willReturn($distances);

        $result = $this->service->calculateShortestPath('MX', 'VUAR');

        $this->assertEquals(['path' => ['MX', 'CGE', 'VUAR'], 'distance' => 1.0], $result);
    }

    public function testCalculateShortestPathStationNotFound(): void
    {
        $this->stationRepository
            ->method('findByShortName')
            ->willReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('One or both stations not found');

        $this->service->calculateShortestPath('UNKNOWN', 'MX');
    }

    public function testCalculateShortestPathNoPathExists(): void
    {
        $station1 = new Station(1, 'MX', 'Montreux');
        $station2 = new Station(2, 'ISOLATED', 'Isolated Station');

        $this->stationRepository
            ->method('findByShortName')
            ->willReturnCallback(fn($name) => match($name) {
                'MX' => $station1,
                'ISOLATED' => $station2,
            });

        $this->distanceRepository
            ->method('findAll')
            ->willReturn([]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No path exists between stations');

        $this->service->calculateShortestPath('MX', 'ISOLATED');
    }
}



