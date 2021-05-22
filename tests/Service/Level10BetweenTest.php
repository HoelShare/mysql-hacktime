<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Constants\Globals;
use App\Constants\Level10;
use App\Constants\Level9;
use Doctrine\DBAL\Exception\DriverException;

class Level10BetweenTest extends KernelTestCase
{
    /**
     * @before
     */
    public function cleanUp()
    {
        try {
            $this->connection->executeQuery(
                sprintf(
                    'DROP VIEW IF EXISTS %s.`%s`;',
                    self::TEST_USER,
                    Level10::VIEW_NAME_MID_MARKET_ORDERS,
                )
            );
        } catch (DriverException $exception) {
        }
    }

    public function testLevel10WrongFilter(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT * FROM %s.`%s`;
SQL
                ,
                self::TEST_USER,
                Level10::VIEW_NAME_MID_MARKET_ORDERS,
                self::TEST_USER,
                Globals::TABLE_NAME_ORDERS,
            )
        );

        $this->assertView();
    }

    public function testLevel10WrongColumns(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT id, tenant_id FROM %s.`%s` WHERE amount_total BETWEEN 150 and 500;
SQL
                ,
                self::TEST_USER,
                Level10::VIEW_NAME_MID_MARKET_ORDERS,
                self::TEST_USER,
                Globals::TABLE_NAME_ORDERS,
            )
        );

        $this->assertView();
    }

    public function testLevel10GreaterLessFilter(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT * FROM %s.`%s` WHERE amount_total >= 150 and amount_total <= 500;
SQL
                ,
                self::TEST_USER,
                Level10::VIEW_NAME_MID_MARKET_ORDERS,
                self::TEST_USER,
                Globals::TABLE_NAME_ORDERS,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame('Try filtering with BETWEEN, so you do not need >= and <=. [amount_total between 150 and 500]', $error);
    }

    public function testLevel10Success(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT * FROM %s.`%s` WHERE amount_total between 150 and 500;
SQL
                ,
                self::TEST_USER,
                Level10::VIEW_NAME_MID_MARKET_ORDERS,
                self::TEST_USER,
                Globals::TABLE_NAME_ORDERS,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}