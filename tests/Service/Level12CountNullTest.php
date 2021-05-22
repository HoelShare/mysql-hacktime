<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Constants\Globals;
use App\Constants\Level10;
use App\Constants\Level9;
use Doctrine\DBAL\Exception\DriverException;

class Level12CountNullTest extends KernelTestCase
{
    public function testLevel12NothingSet(): void
    {
        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame('Nothing set to the solution column! Try counting the orders & update the solution.', $error);
    }

    public function testLevel12TooLessSolution(): void
    {
        $this->connection->executeQuery(
            sprintf(
                'UPDATE %s.level set solution = (select count(0) from %s.order inner join %s.order_address on order_address.order_id = order.id where salutation is null) - 5 where number = 12',
                self::TEST_USER,
                self::TEST_USER,
                self::TEST_USER
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame('Wrong, you counted too less!', $error);
    }

    public function testLevel12TooMuchSolution(): void
    {
        $this->connection->executeQuery(
            sprintf(
                'UPDATE %s.level set solution = (select count(0) from %s.order inner join %s.order_address on order_address.order_id = order.id where salutation is null) + 5 where number = 12',
                self::TEST_USER,
                self::TEST_USER,
                self::TEST_USER
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame('Wrong, you counted too much!', $error);
    }

    public function testLevel12Success(): void
    {
        $this->connection->executeQuery(
            sprintf(
                'UPDATE %s.level set solution = (select count(0) from %s.order inner join %s.order_address on order_address.order_id = order.id where salutation is null) where number = 12',
                self::TEST_USER,
                self::TEST_USER,
                self::TEST_USER
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}