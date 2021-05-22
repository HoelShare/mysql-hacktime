<?php
declare(strict_types=1);

namespace App\Service\DemoData;

use Doctrine\DBAL\Connection;

class Level11Count implements DemoDataInterface
{
    public function create(Connection $connection): void
    {
    }

    public function cleanUp(Connection $connection): void
    {
    }

    public function getLevel(): int
    {
        return 11;
    }

    public function getDescription(): string
    {
        return 'Quiz: How many orders do we know? (Set to solution column)';
    }

    public function validate(Connection $connection, string $username): ?string
    {
        $solution = $connection->fetchOne(
            'SELECT solution from level where number = :number',
            ['number' => $this->getLevel()]
        );

        if ($solution === false || $solution === null || $solution === '') {
            return 'Nothing set to the solution column! Try counting the orders & update the solution.';
        }

        $result = (int) $connection->fetchOne('SELECT count(0) from `order`');

        if ($result < (int) $solution) {
            return 'Wrong, you counted too much, just count the orders!';
        }
        if ($result > (int) $solution) {
            return 'Wrong, count all orders!';
        }

        return null;
    }

    public function reset(Connection $connection): void
    {
    }
}
