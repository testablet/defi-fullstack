<?php

declare(strict_types=1);

namespace App\Command;

use App\Infrastructure\Persistence\Doctrine\Entity\DistanceEntity;
use App\Infrastructure\Persistence\Doctrine\Entity\StationEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:load-stations',
    description: 'Load stations and distances from JSON files'
)]
class LoadStationsCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $stationsFile = __DIR__ . '/../../data/stations.json';
        $distancesFile = __DIR__ . '/../../data/distances.json';

        if (!file_exists($stationsFile)) {
            $io->error('stations.json not found');
            return Command::FAILURE;
        }

        if (!file_exists($distancesFile)) {
            $io->error('distances.json not found');
            return Command::FAILURE;
        }

        $stationsData = json_decode(file_get_contents($stationsFile), true);
        $distancesData = json_decode(file_get_contents($distancesFile), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $io->error('Invalid JSON in data files');
            return Command::FAILURE;
        }

        // Clear existing data
        $this->entityManager->createQuery('DELETE FROM App\Infrastructure\Persistence\Doctrine\Entity\DistanceEntity')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Infrastructure\Persistence\Doctrine\Entity\StationEntity')->execute();

        // Load stations
        $io->progressStart(count($stationsData));
        foreach ($stationsData as $stationData) {
            $station = new StationEntity();
            $station->setId($stationData['id']);
            $station->setShortName($stationData['shortName']);
            $station->setLongName($stationData['longName']);
            $this->entityManager->persist($station);
            $io->progressAdvance();
        }
        $io->progressFinish();
        $io->success(sprintf('Loaded %d stations', count($stationsData)));

        // Load distances
        $totalDistances = 0;
        foreach ($distancesData as $network) {
            $totalDistances += count($network['distances']);
        }
        $io->progressStart($totalDistances);

        foreach ($distancesData as $network) {
            $networkName = $network['name'];
            foreach ($network['distances'] as $distanceData) {
                $distance = new DistanceEntity();
                $distance->setParentStation($distanceData['parent']);
                $distance->setChildStation($distanceData['child']);
                $distance->setDistance($distanceData['distance']);
                $distance->setNetworkName($networkName);
                $this->entityManager->persist($distance);
                $io->progressAdvance();
            }
        }
        $io->progressFinish();

        $this->entityManager->flush();
        $io->success(sprintf('Loaded %d distances', $totalDistances));

        return Command::SUCCESS;
    }
}



