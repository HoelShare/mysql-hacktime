<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Globals;
use App\Constants\Level10;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210522102254 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            sprintf(
                'CREATE VIEW %s AS SELECT * FROM `%s` WHERE amount_total BETWEEN 150 AND 500;',
                Level10::VIEW_NAME_MID_MARKET_ORDERS,
                Globals::TABLE_NAME_ORDERS
            )
        );
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
