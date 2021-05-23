<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Globals;
use App\Constants\Level19;
use App\Constants\Level20;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210523002123 extends AbstractMigration
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
        `company`.`net_promoter_score` AS `net_promoter_score`,
        `company_business_relation`.`name` as `business_relation_name`
    FROM
        %s company
            INNER JOIN
        %s ultimo ON ultimo.ultimo BETWEEN company.valid_from_dttm AND company.valid_to_dttm
            left JOIN company_has_business_relation on company_has_business_relation.company_id = company.id and ultimo.ultimo between company_has_business_relation.valid_from_dttm and company_has_business_relation.valid_to_dttm
            left JOIN company_business_relation on company_business_relation.id = company_has_business_relation.business_relation_id and ultimo.ultimo between company_business_relation.valid_from_dttm and company_business_relation.valid_to_dttm
  WHERE
        ultimo.month_diff BETWEEN - 12 AND 0;
SQL
                ,
                Level20::VIEW_NAME_TO_COMPARE,
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
