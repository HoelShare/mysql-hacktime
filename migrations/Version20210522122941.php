<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Globals;
use App\Constants\Level13;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210522122941 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
            CREATE VIEW %s AS SELECT * FROM `%s` ORDER BY tenant_id, order_date_time DESC;
SQL
                ,
                Level13::EXPECTED_VIEW_NAME,
                Globals::TABLE_NAME_ORDERS,
            )
        );
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
