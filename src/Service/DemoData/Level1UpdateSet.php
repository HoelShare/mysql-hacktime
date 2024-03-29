<?php
declare(strict_types=1);

namespace App\Service\DemoData;

use Doctrine\DBAL\Connection;

class Level1UpdateSet implements DemoDataInterface
{
    public function create(Connection $connection): void
    {
        // nothing to do, the level entry is added automatically
    }

    public function cleanUp(Connection $connection): void
    {
        // nothing to do
    }

    public function getLevel(): int
    {
        return 1;
    }

    public function getDescription(): string
    {
        return 'Task: Set anything to the solution column';
    }

    public function validate(Connection $connection, string $username): ?string
    {
        $countRows = (int) $connection->fetchOne('SELECT count(number) from level where number = 1 and solution is not null');

        if ($countRows !== 1) {
            return 'Try using a command like UPDATE level [] WHERE number = []';
        }

        return null;
    }

    public function reset(Connection $connection): void
    {
        // nth
    }
}
