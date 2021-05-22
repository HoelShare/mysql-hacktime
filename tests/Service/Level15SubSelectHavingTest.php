<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Constants\Globals;
use App\Constants\Level14;
use App\Constants\Level15;
use Doctrine\DBAL\Exception\DriverException;

class Level15SubSelectHavingTest extends KernelTestCase
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
                    Level15::EXPECTED_VIEW_NAME,
                )
            );
        } catch (DriverException $exception) {
        }
    }

    public function testLevel15NoSorting(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT 
                    `%s`.*
                FROM
                    %s.`%s`
                        INNER JOIN
                    (SELECT 
                        order_id, tenant_id
                    FROM
                        order_line_item
                    GROUP BY order_id , tenant_id
                    HAVING COUNT(0) > 2) order_line_item ON `%s`.id = order_line_item.order_id
                        AND `%s`.tenant_id = order_line_item.tenant_id;
SQL
                ,
                self::TEST_USER,
                Level15::EXPECTED_VIEW_NAME,
                Globals::TABLE_NAME_ORDERS,
                self::TEST_USER,
                Globals::TABLE_NAME_ORDERS,
                Globals::TABLE_NAME_ORDERS,
                Globals::TABLE_NAME_ORDERS,
            )
        );

        $this->assertView();
    }

    public function testLevel15WrongColumns(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT 
                    `%s`.id
                FROM
                    %s.`%s`
                        INNER JOIN
                    (SELECT 
                        order_id, tenant_id
                    FROM
                        order_line_item
                    GROUP BY order_id , tenant_id
                    HAVING COUNT(0) > 2) order_line_item ON `%s`.id = order_line_item.order_id
                        AND `%s`.tenant_id = order_line_item.tenant_id
                ORDER BY `%s`.tenant_id , id;
SQL
                ,
                self::TEST_USER,
                Level15::EXPECTED_VIEW_NAME,
                Globals::TABLE_NAME_ORDERS,
                self::TEST_USER,
                Globals::TABLE_NAME_ORDERS,
                Globals::TABLE_NAME_ORDERS,
                Globals::TABLE_NAME_ORDERS,
                Globals::TABLE_NAME_ORDERS,
            )
        );

        $this->assertView();
    }


    public function testLevel15Success(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT 
                    `%s`.*
                FROM
                    %s.`%s`
                        INNER JOIN
                    (SELECT 
                        order_id, tenant_id
                    FROM
                        order_line_item
                    GROUP BY order_id , tenant_id
                    HAVING COUNT(0) > 2) order_line_item ON `%s`.id = order_line_item.order_id
                        AND `%s`.tenant_id = order_line_item.tenant_id
                ORDER BY `%s`.tenant_id , id;
SQL
                ,
                self::TEST_USER,
                Level15::EXPECTED_VIEW_NAME,
                Globals::TABLE_NAME_ORDERS,
                self::TEST_USER,
                Globals::TABLE_NAME_ORDERS,
                Globals::TABLE_NAME_ORDERS,
                Globals::TABLE_NAME_ORDERS,
                Globals::TABLE_NAME_ORDERS,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }

    protected function assertView(): void
    {
        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame(
            'The result seems to be wrong, possible mistakes. The view contains invalid data or wrong column names.
            Hint: Try using a SubSelect with a HAVING clause',
            $error
        );
    }
}