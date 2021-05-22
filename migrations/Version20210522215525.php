<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Globals;
use App\Constants\Level19;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210522215525 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
CREATE VIEW %s AS
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
                Level19::VIEW_NAME_TO_COMPARE,
                Globals::TABLE_COMPANY,
                Globals::VIEW_NAME_ULTIMO,
            )
        );
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
