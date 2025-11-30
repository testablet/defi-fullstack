<?php

declare(strict_types=1);

namespace App\Domain\Routing\Service;

use App\Domain\Distance\Repository\DistanceRepositoryInterface;
use App\Domain\Station\Repository\StationRepositoryInterface;

class DijkstraRoutingService
{
    public function __construct(
        private readonly StationRepositoryInterface $stations,
        private readonly DistanceRepositoryInterface $distances
    ) {
    }

    /**
     * Calculate shortest path between two stations using Dijkstra's algorithm
     *
     * @return array{path: string[], distance: float}
     * @throws \RuntimeException if no path exists
     */
    public function calculateShortestPath(string $fromStationId, string $toStationId): array
    {
        $fromStation = $this->stations->findByShortName($fromStationId);
        $toStation = $this->stations->findByShortName($toStationId);

        if ($fromStation === null || $toStation === null) {
            throw new \RuntimeException('One or both stations not found');
        }

        if ($fromStationId === $toStationId) {
            return ['path' => [$fromStationId], 'distance' => 0.0];
        }

        $graph = $this->buildGraph();
        $distances = [];
        $previous = [];
        $unvisited = [];

        foreach ($graph as $station => $neighbors) {
            $distances[$station] = PHP_FLOAT_MAX;
            $previous[$station] = null;
            $unvisited[$station] = true;
        }

        $distances[$fromStationId] = 0.0;

        while (!empty($unvisited)) {
            $current = $this->getClosestUnvisited($distances, $unvisited);

            if ($current === null || $distances[$current] === PHP_FLOAT_MAX) {
                throw new \RuntimeException('No path exists between stations');
            }

            if ($current === $toStationId) {
                break;
            }

            unset($unvisited[$current]);

            if (!isset($graph[$current])) {
                continue;
            }

            foreach ($graph[$current] as $neighbor => $distance) {
                if (!isset($unvisited[$neighbor])) {
                    continue;
                }

                $alt = $distances[$current] + $distance;
                if ($alt < $distances[$neighbor]) {
                    $distances[$neighbor] = $alt;
                    $previous[$neighbor] = $current;
                }
            }
        }

        $path = $this->reconstructPath($previous, $fromStationId, $toStationId);
        $totalDistance = $distances[$toStationId];

        return ['path' => $path, 'distance' => $totalDistance];
    }

    /**
     * Build adjacency graph from distances
     *
     * @return array<string, array<string, float>>
     */
    private function buildGraph(): array
    {
        $graph = [];
        $allDistances = $this->distances->findAll();

        foreach ($allDistances as $distance) {
            $parent = $distance->getParentStation();
            $child = $distance->getChildStation();
            $dist = $distance->getDistance();

            if (!isset($graph[$parent])) {
                $graph[$parent] = [];
            }
            $graph[$parent][$child] = $dist;

            // Make graph bidirectional
            if (!isset($graph[$child])) {
                $graph[$child] = [];
            }
            $graph[$child][$parent] = $dist;
        }

        return $graph;
    }

    /**
     * Get closest unvisited station
     */
    private function getClosestUnvisited(array $distances, array $unvisited): ?string
    {
        $minDistance = PHP_FLOAT_MAX;
        $closest = null;

        foreach ($unvisited as $station => $_) {
            if ($distances[$station] < $minDistance) {
                $minDistance = $distances[$station];
                $closest = $station;
            }
        }

        return $closest;
    }

    /**
     * Reconstruct path from previous array
     *
     * @return string[]
     */
    private function reconstructPath(array $previous, string $from, string $to): array
    {
        $path = [];
        $current = $to;

        while ($current !== null) {
            array_unshift($path, $current);
            $current = $previous[$current] ?? null;
        }

        if ($path[0] !== $from) {
            throw new \RuntimeException('Path reconstruction failed');
        }

        return $path;
    }
}



