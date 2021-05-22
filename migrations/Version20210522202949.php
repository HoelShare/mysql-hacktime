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
            sprintf(<<<'SQL'
CREATE VIEW `%s` AS
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
    `%s`.`net_promoter_score` AS `net_promoter_score`,
    `%s`.current_flag = 0 AS `is_deleted`
FROM
    %s
WHERE
    id NOT IN (SELECT 
            %s.id
        FROM
            %s
        WHERE
            test = 1)
        AND (id , valid_from_dttm) IN (SELECT 
            id, MAX(VALID_FROM_DTTM)
        FROM
            %s
        GROUP BY id);
SQL
            ,
            Level17::VIEW_NAME_TO_COMPARE,
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
            Globals::TABLE_COMPANY,
            Globals::TABLE_COMPANY,
            Globals::TABLE_COMPANY,
            Globals::TABLE_COMPANY,
            Globals::TABLE_COMPANY,
            )
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
