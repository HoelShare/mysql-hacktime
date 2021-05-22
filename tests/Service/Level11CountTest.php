<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Constants\Globals;
use App\Constants\Level10;
use App\Constants\Level9;
use Doctrine\DBAL\Exception\DriverException;

class Level11CountTest extends KernelTestCase
{
    public function testLevel11NothingSet(): void
    {
        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame('Nothing set to the solution column! Try counting the orders & update the solution.', $error);
    }

    public function testLevel11TooLessSolution(): void
    {
        $this->connection->executeQuery(
            sprintf(
                'UPDATE %s.level set solution = (select count(0) from %s.order) - 5 where number = 11',
                self::TEST_USER,
                self::TEST_USER
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame('Wrong, count all orders!', $error);
    }

    public function testLevel11WrongSolution(): void
    {
        $this->connection->executeQuery(
            sprintf(
                'UPDATE %s.level set solution = 5 + (select count(0) from %s.order) where number = 11',
                self::TEST_USER,
                self::TEST_USER
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame('Wrong, you counted too much, just count the orders!', $error);
    }

    public function testLevel11Success(): void
    {
        $this->connection->executeQuery(
            sprintf(
                'UPDATE %s.level set solution = (select count(0) from %s.order) where number = 11',
                self::TEST_USER,
                self::TEST_USER
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}