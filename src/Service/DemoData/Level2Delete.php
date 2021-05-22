<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use Doctrine\DBAL\Connection;
use Faker\Factory;

class Level2Delete implements DemoDataInterface
{
    public function create(Connection $connection): void
    {
        $faker = Factory::create();
        $connection->executeQuery(
            <<<'SQL'
            DROP TABLE IF EXISTS level2;
            CREATE TABLE level2 (
                id int auto_increment not null,
                name varchar(250) not null,
             primary key(id));
SQL
        );

        for ($i = 0; $i < 10; $i++) {
            $connection->executeQuery('INSERT INTO level2 (name) VALUES (:name)', ['name' => $faker->name()]);
        }
    }

    public function cleanUp(Connection $connection): void
    {
        $connection->executeQuery('DROP TABLE level2');
    }

    public function getLevel(): int
    {
        return 2;
    }

    public function getDescription(): string
    {
        return 'Delete all rows of table level2';
    }

    public function validate(Connection $connection, string $username): ?string
    {
        $countRows = (int) $connection->fetchOne(
            'SELECT count(0) from level2'
        );

        if ($countRows === 10) {
            return 'You should experiment with the DELETE command. Did you use the correct table?';
        }

        if ($countRows !== 0) {
            return 'You need to delete all rows!';
        }

        return null;
    }

    public function reset(Connection $connection): void
    {
        $this->cleanUp($connection);
    }
}
