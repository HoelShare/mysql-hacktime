<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Level7;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210522044436 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            <<<'SQL'
            CREATE TABLE star_wars_character (id int not null primary key, name varchar(250) not null, height int not null, gender varchar(50) null);
            CREATE TABLE star_wars_star_ship (id int not null primary key, name varchar(250) not null, passenger int null, manufacturer varchar(250) not null, length numeric(10,2));
            CREATE TABLE star_wars_ship_pilot (pilot_id int not null, ship_id int not null, primary key (pilot_id, ship_id), foreign key (pilot_id) references star_wars_character (id), foreign key (ship_id) references star_wars_star_ship (id));
            CREATE VIEW star_wars_character_ships as SELECT c.id as pilot_id, c.name as pilot_name, s.id as ship_id, s.name as ship_name, s.manufacturer as ship_manufacturer FROM star_wars_character c INNER JOIN star_wars_ship_pilot swsp on c.id = swsp.pilot_id INNER JOIN star_wars_star_ship s on swsp.ship_id = s.id;
SQL
        );

        foreach (Level7::PEOPLE as $person) {
            $this->connection->insert('star_wars_character', $person);
        }
        foreach (Level7::STAR_SHIPS as $ship) {
            $this->connection->insert('star_wars_star_ship', $ship);
        }
        foreach (Level7::SHIP_PILOT as $shipPilot) {
            $this->connection->insert('star_wars_ship_pilot', $shipPilot);
        }
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
