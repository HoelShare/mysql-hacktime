<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Level8;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210522065953 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            sprintf(<<<'SQL'
    CREATE VIEW %s as 
        SELECT c.id as pilot_id, c.name as pilot_name, 
               s.id as ship_id, s.name as ship_name, s.manufacturer as ship_manufacturer 
        FROM star_wars_character c 
            LEFT OUTER JOIN star_wars_ship_pilot swsp on c.id = swsp.pilot_id 
            LEFT OUTER JOIN star_wars_star_ship s on swsp.ship_id = s.id
SQL
            , Level8::VIEW_NAME_TO_COMPARE)
        );
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
