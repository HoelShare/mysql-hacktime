<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Constants\Globals;
use App\Constants\Level14;
use App\Constants\Level15;
use App\Constants\Level16;
use Doctrine\DBAL\Exception\DriverException;

class Level16HistorizedTableTest extends KernelTestCase
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
                    Level16::EXPECTED_VIEW_NAME,
                )
            );
        } catch (DriverException $exception) {
        }
    }

    public function testLevel16WrongFilter(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE VIEW %s.`%s` AS
    SELECT 
        `%s`.`id` AS `id`,
        `%s`.`name` AS `name`,
        `%s`.`customer_number` AS `customer_number`,
        `%s`.`creation_date` AS `creation_date`,
        `%s`.`website` AS `website`,
        `%s`.`banned` AS `banned`,
        `%s`.`test` AS `test`,
        `%s`.`notice` AS `notice`,
        `%s`.`verified` AS `verified`,
        `%s`.`net_promoter_score` AS `net_promoter_score`
    FROM
        %s.`%s`
    WHERE
        current_flag = 0
            AND id NOT IN (SELECT 
                id
            FROM
                %s.%s
            WHERE
                test = 1);
SQL
                ,
                self::TEST_USER,
                Level16::EXPECTED_VIEW_NAME,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                self::TEST_USER,
                Globals::TABLE_COMPANY,
                self::TEST_USER,
                Globals::TABLE_COMPANY,
            )
        );

        $this->assertView();
    }

    public function testLevel15WrongColumns(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE VIEW %s.`%s` AS
    SELECT 
        `%s`.`id` AS `id`
    FROM
        %s.`%s`
    WHERE
        current_flag = 1
            AND id NOT IN (SELECT 
                id
            FROM
                %s.%s
            WHERE
                test = 1);
SQL
                ,
                self::TEST_USER,
                Level16::EXPECTED_VIEW_NAME,
                Globals::TABLE_COMPANY,
                self::TEST_USER,
                Globals::TABLE_COMPANY,
                self::TEST_USER,
                Globals::TABLE_COMPANY,
            )
        );

        $this->assertView();
    }


    public function testLevel15Success(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE VIEW %s.`%s` AS
    SELECT 
        `%s`.`id` AS `id`,
        `%s`.`name` AS `name`,
        `%s`.`customer_number` AS `customer_number`,
        `%s`.`creation_date` AS `creation_date`,
        `%s`.`website` AS `website`,
        `%s`.`banned` AS `banned`,
        `%s`.`test` AS `test`,
        `%s`.`notice` AS `notice`,
        `%s`.`verified` AS `verified`,
        `%s`.`net_promoter_score` AS `net_promoter_score`
    FROM
        %s.`%s`
    WHERE
        current_flag = 1
            AND id NOT IN (SELECT 
                id
            FROM
                %s.%s
            WHERE
                test = 1);
SQL
                ,
                self::TEST_USER,
                Level16::EXPECTED_VIEW_NAME,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                self::TEST_USER,
                Globals::TABLE_COMPANY,
                self::TEST_USER,
                Globals::TABLE_COMPANY,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}