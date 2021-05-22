<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Globals;
use App\Constants\Level14;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210522124305 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
            CREATE VIEW %s AS SELECT tenant_id, SUM(amount_total) as total_amount, COUNT(0) as order_count FROM `%s` GROUP BY tenant_id order by 2 desc; 
SQL
                ,
                Level14::EXPECTED_VIEW_NAME,
                Globals::TABLE_NAME_ORDERS,
            )
        );
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
