<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Constants\Globals;
use App\Constants\Level14;
use App\Constants\Level15;
use App\Constants\Level16;
use App\Constants\Level19;
use Doctrine\DBAL\Exception\DriverException;

class Level19CompanyUltimoTest extends KernelTestCase
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
                    Level19::EXPECTED_VIEW_NAME,
                )
            );
        } catch (DriverException $exception) {
        }
    }

    public function testLevel19WrongFilter(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
CREATE VIEW %s.%s AS
    SELECT 
        ultimo.ultimo,
        `company`.`id` AS `id`,
        `company`.`name` AS `name`,
        `company`.`customer_number` AS `customer_number`,
        `company`.`creation_date` AS `creation_date`,
        `company`.`website` AS `website`,
        `company`.`banned` AS `banned`,
        `company`.`test` AS `test`,
        `company`.`notice` AS `notice`,
        `company`.`verified` AS `verified`,
        `company`.`net_promoter_score` AS `net_promoter_score`
    FROM
        %s company
            INNER JOIN
        %s ultimo ON ultimo.ultimo BETWEEN company.valid_from_dttm AND company.valid_to_dttm
    WHERE
        ultimo.month_diff BETWEEN - 6 AND 0;
SQL
                ,
                self::TEST_USER,
                Level19::EXPECTED_VIEW_NAME,
                Globals::TABLE_COMPANY,
                Globals::VIEW_NAME_ULTIMO,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame(
            'The result seems to be wrong, did you filter the expected?',
            $error
        );
    }

    public function testLevel19WrongColumns(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
CREATE VIEW %s.%s AS
    SELECT 
        ultimo.ultimo,
        `company`.`id` AS `id`
    FROM
        %s company
            INNER JOIN
        %s ultimo ON ultimo.ultimo BETWEEN company.valid_from_dttm AND company.valid_to_dttm
    WHERE
        ultimo.month_diff BETWEEN - 12 AND 0;
SQL
                ,
                self::TEST_USER,
                Level19::EXPECTED_VIEW_NAME,
                Globals::TABLE_COMPANY,
                Globals::VIEW_NAME_ULTIMO,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame(
            'Did you name the columns as expected?
            ultimo,id,name,customer_number,creation_date,website,banned,test,notice,verified,net_promoter_score',
            $error
        );
    }


    public function testLevel19Success(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
CREATE VIEW %s.%s AS
    SELECT 
        ultimo.ultimo,
        `company`.`id` AS `id`,
        `company`.`name` AS `name`,
        `company`.`customer_number` AS `customer_number`,
        `company`.`creation_date` AS `creation_date`,
        `company`.`website` AS `website`,
        `company`.`banned` AS `banned`,
        `company`.`test` AS `test`,
        `company`.`notice` AS `notice`,
        `company`.`verified` AS `verified`,
        `company`.`net_promoter_score` AS `net_promoter_score`
    FROM
        %s company
            INNER JOIN
        %s ultimo ON ultimo.ultimo BETWEEN company.valid_from_dttm AND company.valid_to_dttm
    WHERE
        ultimo.month_diff BETWEEN - 12 AND 0;
SQL
                ,
                self::TEST_USER,
                Level19::EXPECTED_VIEW_NAME,
                Globals::TABLE_COMPANY,
                Globals::VIEW_NAME_ULTIMO,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}