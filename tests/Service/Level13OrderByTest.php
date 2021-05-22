<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Constants\Globals;
use App\Constants\Level10;
use App\Constants\Level13;
use App\Constants\Level9;
use Doctrine\DBAL\Exception\DriverException;

class Level13OrderByTest extends KernelTestCase
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
                    Level13::EXPECTED_VIEW_NAME,
                )
            );
        } catch (DriverException $exception) {
        }
    }

    public function testLevel13NoSorting(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT * FROM %s.`%s`;
SQL
                ,
                self::TEST_USER,
                Level13::EXPECTED_VIEW_NAME,
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
                    SELECT id, tenant_id FROM %s.`%s` ORDER BY tenant_id, order_date_time DESC;
SQL
                ,
                self::TEST_USER,
                Level13::EXPECTED_VIEW_NAME,
                self::TEST_USER,
                Globals::TABLE_NAME_ORDERS,
            )
        );

        $this->assertView();
    }


    public function testLevel13Success(): void
    {
        $this->connection->executeQuery(
            sprintf(
                'CREATE VIEW %s.%s AS SELECT * FROM %s.`%s` ORDER BY tenant_id, order_date_time DESC;',
                self::TEST_USER,
                Level13::EXPECTED_VIEW_NAME,
                self::TEST_USER,
                Globals::TABLE_NAME_ORDERS,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}