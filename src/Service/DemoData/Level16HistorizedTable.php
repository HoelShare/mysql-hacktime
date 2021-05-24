<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Globals;
use App\Constants\Level16;
use Doctrine\DBAL\Connection;

class Level16HistorizedTable extends ViewCompareLevel
{
    public function create(Connection $connection): void
    {
        $connection->executeQuery(
            <<<'SQL'
CREATE TABLE `company` (
   `id` int NOT NULL,
   `name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
   `customer_number` int NOT NULL,
   `creation_date` datetime NOT NULL,
   `website` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
   `banned` tinyint(1) NOT NULL DEFAULT '0',
   `test` tinyint(1) NOT NULL DEFAULT '0',
   `notice` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
   `verified` tinyint(1) NOT NULL DEFAULT '0',
   `net_promoter_score` int DEFAULT NULL,
   `VALID_FROM_DTTM` datetime NOT NULL,
   `VALID_TO_DTTM` datetime NOT NULL,
   `CURRENT_FLAG` tinyint(1) DEFAULT NULL,
   PRIMARY KEY (`id`,`VALID_FROM_DTTM`),
   UNIQUE KEY `id` (`id`,`VALID_TO_DTTM`),
   KEY `id_2` (`id`,`CURRENT_FLAG`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );

        $this->rootConnection->executeQuery(
            sprintf(
                <<<'SQL'
        INSERT INTO %s.%s 
SELECT * FROM %s
SQL
                ,
                $connection->getDatabase(),
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
            )
        );

        $connection->executeQuery(
            sprintf(
                <<<'SQL'
            CREATE OR REPLACE VIEW %s AS
        SELECT 
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
        `%s` company
SQL
                ,
                Level16::EXPECTED_VIEW_NAME,
                Globals::TABLE_COMPANY,
            )
        );
    }

    public function cleanUp(Connection $connection): void
    {
    }

    public function getLevel(): int
    {
        return 16;
    }

    public function getDescription(): string
    {
        return sprintf(
            'The company data is historically stored in the database, but for reporting we need only the current companies (current_flag = 1), and we should exclude all companies that were ever a test company (test = 1).
        A view (%s) was created for this use case, but it had some filtering issues, please fix them.',
            Level16::EXPECTED_VIEW_NAME
        );
    }

    protected function getMainViewName(): string
    {
        return Level16::EXPECTED_VIEW_NAME;
    }

    public function validate(Connection $connection, string $username): ?string
    {
        return $this->validateView($connection, Level16::EXPECTED_VIEW_NAME);
    }

    public function reset(Connection $connection): void
    {
        $connection->executeQuery(
            <<<'SQL'
            DROP TABLE IF EXISTS company;
SQL
        );
    }
}
