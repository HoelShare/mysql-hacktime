<?php
declare(strict_types=1);

namespace App\Service\DemoData;

use Doctrine\DBAL\Connection;

class Level12CountIsNull implements DemoDataInterface
{
    public function create(Connection $connection): void
    {
    }

    public function cleanUp(Connection $connection): void
    {
    }

    public function getLevel(): int
    {
        return 12;
    }

    public function getDescription(): string
    {
        return 'Quiz: How many orders exists where the Salutation of the address is not set? (Set to solution column)';
    }

    public function validate(Connection $connection, string $username): ?string
    {
        $solution = $connection->fetchOne(
            'SELECT solution from level where number = :number',
            ['number' => $this->getLevel()]
        );

        if ($solution === false ||$solution === null || $solution === '') {
            return 'Nothing set to the solution column! Try counting the orders & update the solution.';
        }

        $result = (int)$connection->fetchOne('SELECT count(0) from `order` inner join order_address on order_address.order_id = order.id where salutation is null');

        if ($result < (int)$solution) {
            return 'Wrong, you counted too much!';
        }
        if ($result > (int)$solution) {
            return 'Wrong, you counted too less!';
        }

        return null;
    }

    public function reset(Connection $connection): void
    {
    }
}