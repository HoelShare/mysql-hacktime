<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Constants\Level9;
use Doctrine\DBAL\Exception\DriverException;

class Level9InTest extends KernelTestCase
{
    /**
     * @before
     */
    public function cleanUp()
    {
        try {
            $this->connection->executeQuery(
                sprintf(
                    'DROP VIEW IF EXISTS %s.%s;',
                    self::TEST_USER,
                    Level9::VIEW_NAME_MONDAY_WEDNESDAY_SATURDAY,
                )
            );
        } catch (DriverException $exception) {
        }
    }

    public function testLevel9WrongFilter(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT * FROM %s.%s;
SQL
                ,
                self::TEST_USER,
                Level9::VIEW_NAME_MONDAY_WEDNESDAY_SATURDAY,
                self::TEST_USER,
                Level9::DATE_TABLE,
            )
        );

        $this->assertView();
    }

    public function testLevel8WrongColumns(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT date FROM %s.%s WHERE weekday in (0, 2, 5);
SQL
                ,
                self::TEST_USER,
                Level9::VIEW_NAME_MONDAY_WEDNESDAY_SATURDAY,
                self::TEST_USER,
                Level9::DATE_TABLE,
            )
        );

        $this->assertView();
    }

    public function testLevel8OrFilter(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT * FROM %s.%s WHERE weekday = 0 or weekday = 2 or weekday = 5;
SQL
                ,
                self::TEST_USER,
                Level9::VIEW_NAME_MONDAY_WEDNESDAY_SATURDAY,
                self::TEST_USER,
                Level9::DATE_TABLE,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame('Try filtering with IN. [weekday IN (0, 2, 5)]', $error);
    }

    public function testLevel9Success(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT * FROM %s.%s WHERE weekday in (0, 2, 5);
SQL
                ,
                self::TEST_USER,
                Level9::VIEW_NAME_MONDAY_WEDNESDAY_SATURDAY,
                self::TEST_USER,
                Level9::DATE_TABLE,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}