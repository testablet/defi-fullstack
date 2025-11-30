<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create stations, distances and routes tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE stations (
            id INTEGER NOT NULL,
            short_name VARCHAR(10) NOT NULL,
            long_name VARCHAR(255) NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_stations_short_name ON stations (short_name)');

        $this->addSql('CREATE TABLE distances (
            id SERIAL NOT NULL,
            parent_station VARCHAR(10) NOT NULL,
            child_station VARCHAR(10) NOT NULL,
            distance DOUBLE PRECISION NOT NULL,
            network_name VARCHAR(50) NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_parent_station ON distances (parent_station)');
        $this->addSql('CREATE INDEX idx_child_station ON distances (child_station)');

        $this->addSql('CREATE TABLE routes (
            id VARCHAR(36) NOT NULL,
            from_station_id VARCHAR(10) NOT NULL,
            to_station_id VARCHAR(10) NOT NULL,
            analytic_code VARCHAR(100) NOT NULL,
            distance_km DOUBLE PRECISION NOT NULL,
            path JSON NOT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX idx_analytic_code ON routes (analytic_code)');
        $this->addSql('CREATE INDEX idx_created_at ON routes (created_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE routes');
        $this->addSql('DROP TABLE distances');
        $this->addSql('DROP TABLE stations');
    }
}



