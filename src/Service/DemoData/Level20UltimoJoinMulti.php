<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Globals;
use App\Constants\Level19;
use App\Constants\Level20;
use Doctrine\DBAL\Connection;

class Level20UltimoJoinMulti extends ViewCompareLevel
{
    public function create(Connection $connection): void
    {
        $connection->executeQuery(
            <<<'SQL'
            CREATE TABLE company_business_relation(
                id int not null,
                name varchar(250) not null,
                description tinytext null,
                prio_sort int not null,
                VALID_FROM_DTTM datetime not null ,
                VALID_TO_DTTM datetime not null,
                CURRENT_FLAG tinyint(1) not null,
                primary key (id, VALID_FROM_DTTM),
                unique key (id, VALID_TO_DTTM),
                key (id, CURRENT_FLAG)
            );

            CREATE TABLE company_has_business_relation(
                company_id int not null,
                business_relation_id int not null,
                VALID_FROM_DTTM datetime not null,
                VALID_TO_DTTM datetime NOT null,
                CURRENT_FLAG tinyint(1) NOT NULL,
                primary key (company_id, business_relation_id, VALID_FROM_DTTM),
                unique key (company_id, business_relation_id, VALID_TO_DTTM),
                key (company_id, business_relation_id, CURRENT_FLAG)
             );
SQL
        );

        foreach (['company_business_relation', 'company_has_business_relation'] as $tableName) {
            $this->rootConnection->executeQuery(
                sprintf(
                    <<<'SQL'
        INSERT INTO %s.%s 
SELECT * FROM %s
SQL
                    ,
                    $connection->getDatabase(),
                    $tableName,
                    $tableName,
                )
            );
        }
    }

    public function cleanUp(Connection $connection): void
    {
    }

    public function getLevel(): int
    {
        return 20;
    }

    public function getDescription(): string
    {
        return sprintf(
            'We need to enhance the %s view to also include the business relation name.
            Column: business_realtion_name',
            Level19::EXPECTED_VIEW_NAME,
        );
    }

    protected function getMainViewName(): string
    {
        return Level20::VIEW_NAME_TO_COMPARE;
    }

    public function validate(Connection $connection, string $username): ?string
    {
        return $this->checkResultSame($connection, Level19::EXPECTED_VIEW_NAME);
    }

    public function reset(Connection $connection): void
    {
        $connection->executeQuery(
            sprintf(
                <<<'SQL'
CREATE OR REPLACE VIEW %s AS
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
                Level19::EXPECTED_VIEW_NAME,
                Globals::TABLE_COMPANY,
                Globals::VIEW_NAME_ULTIMO,
            )
        );
    }
}
