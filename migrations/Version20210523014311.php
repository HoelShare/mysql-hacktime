<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Globals;
use App\Constants\Level22;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210523014311 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
CREATE VIEW %s AS
SELECT 
    event_log.*,
    to_days(created_at) - to_days(first_value(created_at) over w) as `days_since_first_log`
FROM
    %s event_log
    window w as (partition by tenant_id order by created_at)
SQL
                ,
                Level22::EXPECTED_VIEW_NAME,
                Globals::TABLE_EVENT_LOG,
            )
        );
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
