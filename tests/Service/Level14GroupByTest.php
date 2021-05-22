<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Constants\Globals;
use App\Constants\Level14;
use Doctrine\DBAL\Exception\DriverException;

class Level14GroupByTest extends KernelTestCase
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
                    Level14::EXPECTED_VIEW_NAME,
                )
            );
        } catch (DriverException $exception) {
        }
    }

    public function testLevel14NoSorting(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT tenant_id, sum(amount_total) as total_amount, count(0) as order_count FROM %s.`%s` GROUP BY tenant_id;
SQL
                ,
                self::TEST_USER,
                Level14::EXPECTED_VIEW_NAME,
                self::TEST_USER,
                Globals::TABLE_NAME_ORDERS,
            )
        );

        $this->assertView();
    }

    public function testLevel13WrongColumns(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT tenant_id as `tenant`, sum(amount_total) as total_amount, count(0) as order_count FROM %s.`%s` GROUP BY tenant_id ORDER BY 2 DESC;
SQL
                ,
                self::TEST_USER,
                Level14::EXPECTED_VIEW_NAME,
                self::TEST_USER,
                Globals::TABLE_NAME_ORDERS,
            )
        );

        $this->assertView();
    }


    public function testLevel14Success(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT tenant_id, sum(amount_total) as total_amount, count(0) as order_count FROM %s.`%s` GROUP BY tenant_id ORDER BY 2 DESC;
SQL
                ,
                self::TEST_USER,
                Level14::EXPECTED_VIEW_NAME,
                self::TEST_USER,
                Globals::TABLE_NAME_ORDERS,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}