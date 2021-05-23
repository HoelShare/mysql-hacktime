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

    public function validate(Connection $connection, string $username): ?string
    {
        return $this->checkResultSame($connection, Level19::EXPECTED_VIEW_NAME, Level20::VIEW_NAME_TO_COMPARE);
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
