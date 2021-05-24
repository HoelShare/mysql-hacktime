<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Globals;
use App\Constants\Level21;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210523010330 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
CREATE VIEW %s 
AS
SELECT 
    id, tenant_id, created_at, data->>"$.keyword" as `keyword`
FROM
    %s
WHERE
    type = 'search';
SQL
                ,
                Level21::EXPECTED_VIEW_NAME,
                Globals::TABLE_EVENT_LOG,
            )
        );
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
