<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Globals;
use App\Constants\Level15;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210522131658 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
CREATE VIEW %s AS
SELECT 
    `%s`.*
FROM
    `%s`
        INNER JOIN
    (SELECT 
        order_id, tenant_id
    FROM
        order_line_item
    GROUP BY order_id , tenant_id
    HAVING COUNT(0) > 2) order_line_item ON `%s`.id = order_line_item.order_id
        AND `%s`.tenant_id = order_line_item.tenant_id
ORDER BY `%s`.tenant_id , id;
SQL
                ,
                Level15::EXPECTED_VIEW_NAME,
                Globals::TABLE_NAME_ORDERS,
                Globals::TABLE_NAME_ORDERS,
                Globals::TABLE_NAME_ORDERS,
                Globals::TABLE_NAME_ORDERS,
                Globals::TABLE_NAME_ORDERS,
            )
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
