<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Globals;
use App\Constants\Level21;
use Doctrine\DBAL\Connection;

class Level21JsonExtract extends ViewCompareLevel
{
    public function create(Connection $connection): void
    {
        $connection->executeQuery(
            sprintf(
                <<<'SQL'
            Drop table if exists %s;         
            create table %s(
                id int not null,
                tenant_id int not null,
                created_at datetime not null,
                type varchar(250) not null,
                data JSON null,
                primary key (id, tenant_id),
                foreign key (tenant_id) references tenant (id)
            );
SQL
                ,
                Globals::TABLE_EVENT_LOG,
                Globals::TABLE_EVENT_LOG,
            )
        );

        $this->rootConnection->executeQuery(
            sprintf(
                <<<'SQL'
        INSERT INTO %s.%s 
SELECT * FROM %s
SQL
                ,
                $connection->getDatabase(),
                Globals::TABLE_EVENT_LOG,
                Globals::TABLE_EVENT_LOG,
            )
        );
    }

    public function cleanUp(Connection $connection): void
    {
    }

    public function getLevel(): int
    {
        return 21;
    }

    public function getDescription(): string
    {
        return sprintf(
            'Extract all search terms from the %s and prepare them in a "%s" view.
            Columns: ',
            Globals::TABLE_EVENT_LOG,
            Level21::EXPECTED_VIEW_NAME,
        );
    }

    public function validate(Connection $connection, string $username): ?string
    {
        return $this->checkResultSame($connection, Level21::EXPECTED_VIEW_NAME, Level21::EXPECTED_VIEW_NAME);
    }

    public function reset(Connection $connection): void
    {
    }
}
