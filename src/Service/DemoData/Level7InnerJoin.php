<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Level6;
use App\Constants\Level7;
use Doctrine\DBAL\Connection;

class Level7InnerJoin extends ViewCompareLevel
{
    public function create(Connection $connection): void
    {
        $connection->executeQuery(
            <<<'SQL'
            CREATE TABLE star_wars_character (id int not null primary key, name varchar(250) not null, height int not null, gender varchar(50) null);
            CREATE TABLE star_wars_star_ship (id int not null primary key, name varchar(250) not null, passenger int null, manufacturer varchar(250) not null, length numeric(10,2));
            CREATE TABLE star_wars_ship_pilot (pilot_id int not null, ship_id int not null, primary key (pilot_id, ship_id), foreign key (pilot_id) references star_wars_character (id), foreign key (ship_id) references star_wars_star_ship (id));
SQL
        );

        foreach (Level7::PEOPLE as $person) {
            $connection->insert('star_wars_character', $person);
        }
        foreach (Level7::STAR_SHIPS as $ship) {
            $connection->insert('star_wars_star_ship', $ship);
        }
        foreach (Level7::SHIP_PILOT as $shipPilot) {
            $connection->insert('star_wars_ship_pilot', $shipPilot);
        }
    }

    public function cleanUp(Connection $connection): void
    {
    }

    public function getLevel(): int
    {
        return 7;
    }

    public function getDescription(): string
    {
        return sprintf(
            'We need a compact overview of which pilot flies which starship.
            Viewname: %s
            Columns: pilot_id, pilot_name, ship_id, ship_name, ship_manufacturer',
            Level7::EXPECTED_VIEW_NAME,
        );
    }

    public function validate(Connection $connection, string $username): ?string
    {
        return $this->validateView(
            $connection,
            $username,
            Level7::EXPECTED_VIEW_NAME,
            Level7::EXPECTED_VIEW_NAME
        );
    }

    public function reset(Connection $connection): void
    {
        $connection->executeQuery(
          <<<'SQL'
            DROP TABLE IF EXISTS star_wars_ship_pilot;
            DROP TABLE IF EXISTS star_wars_star_ship;
            DROP TABLE IF EXISTS star_wars_character;
SQL
        );
    }
}