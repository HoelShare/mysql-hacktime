<?php
declare(strict_types=1);

namespace App\Service\DemoData;

use Doctrine\DBAL\Connection;

class Level0InitialSet implements DemoDataInterface
{
    public function create(Connection $connection): void
    {
        $connection->executeQuery(
            <<<'SQL'
            CREATE TABLE level (
                number int not null,
                description mediumtext null,
                solution varchar(250) null,
                primary key (number)
            );
SQL
        );
    }

    public function reset(Connection $connection): void
    {
        $connection->executeQuery(<<<'SQL'
            DROP TABLE IF EXISTS level;
SQL
        );
    }

    public function cleanUp(Connection $connection): void
    {
        $connection->executeQuery(<<<'SQL'
        DELETE FROM level where number = 1
SQL
        );
    }

    public function getLevel(): int
    {
        return 0;
    }

    public function getDescription(): string
    {
        return 'Task: Insert a new Level record with number = 1. If you think you completed the level, go back to the website and press check!';
    }

    public function validate(Connection $connection, string $username): ?string
    {
        $countRows = (int) $connection->fetchOne('SELECT count(number) from level where number = 1');
        if ($countRows !== 1) {
            return 'Try using an INSERT INTO level [...] command';
        }

        return null;
    }
}
