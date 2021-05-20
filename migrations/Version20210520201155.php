<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210520201155 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            <<<'SQL'
            CREATE TABLE `settings`.`joke` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `joke` MEDIUMTEXT NULL,
              `punchline` MEDIUMTEXT NULL,
              `source` varchar(45) NOT NULL,
              PRIMARY KEY (`id`));
SQL
        );

        $this->connection->insert(
            'joke',
            [
                'joke' => 'A SQL query goes to a restaurant, walks up to 2 tables and says',
                'punchline' => '"Can I join you?"?',
                'source' => 'u/manantyagi25',
            ]
        );
        $this->connection->insert(
            'joke',
            [
                'joke' => 'I saw a great movie about databases.',
                'punchline' => 'I can’t wait for the SQL.',
                'source' => 'u/viky_boy',
            ]
        );
        $this->connection->insert(
            'joke',
            [
                'joke' => 'So, an SQL statement walks into a furniture store...',
                'punchline' => 'The owner yells at him; "You stay away from my tables! You dropped one the last time you were here!"

Sql statement leaves and wanders into a nearby restaurant. He approaches two tables; "I was just chased out of the furniture store, may I join you?"',
                'source' => 'u/quintiza',
            ]
        );
        $this->connection->insert(
            'joke',
            [
                'joke' => '3 Database SQL walked into a NoSQL bar.
                A little while later...
                they walked out',
                'punchline' => 'Because they couldn\'t find a table',
                'source' => 'unknown',
            ]
        );
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
