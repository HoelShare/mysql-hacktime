<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Globals;
use App\Constants\Level17;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210522202949 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
CREATE VIEW `%s` AS
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
    `company`.`net_promoter_score` AS `net_promoter_score`,
    `company`.`current_flag` = 0 AS `is_deleted`
FROM
    %s company
WHERE
    id NOT IN (SELECT 
            company.id
        FROM
            %s company
        WHERE
            test = 1)
        AND (id , valid_from_dttm) IN (SELECT 
            id, MAX(VALID_FROM_DTTM)
        FROM
            %s company
        GROUP BY id);
SQL
                ,
                Level17::VIEW_NAME_TO_COMPARE,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
                Globals::TABLE_COMPANY,
            )
        );
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
