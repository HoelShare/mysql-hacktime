<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Globals;
use App\Constants\Level16;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210522200301 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
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
        `%s`.`net_promoter_score` AS `net_promoter_score`
    FROM
        `%s`
    WHERE
        current_flag = 1
            AND id NOT IN (SELECT 
                id
            FROM
                %s
            WHERE
                test = 1)
SQL
                ,
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
