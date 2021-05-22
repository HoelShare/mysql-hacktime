<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210522144933 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(<<<'SQL'
CREATE OR REPLACE view ultimo as 
with recursive cte(ultimo) as (
SELECT 
    ADDTIME((LAST_DAY(NOW()) - INTERVAL 6 YEAR - INTERVAL 1 MONTH),
            '23:59:59')
    union
    select ADDTIME(CONVERT(LAST_DAY(cte.ultimo + INTERVAL 1 MONTH), DATETIME), '23:59:59') FROM cte where cte.ultimo < NOW() + INTERVAL 1 YEAR)
select ultimo, CONVERT(DATE(ultimo + INTERVAL 1 DAY - INTERVAL 1 MONTH), DATETIME) AS `month_start`, TIMESTAMPDIFF(YEAR, NOW(), ultimo) as `year_diff`, TIMESTAMPDIFF(MONTH, NOW(), ultimo) as `month_diff`, TIMESTAMPDIFF(DAY, NOW(), ultimo) as `day_diff` from cte;          
SQL
        );
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
