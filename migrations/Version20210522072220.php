<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Level9;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210522072220 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
            CREATE TABLE %s as
            WITH RECURSIVE relative_date (d) AS 
            ( SELECT CURRENT_DATE() - INTERVAL 2 YEAR UNION SELECT d + INTERVAL 1 DAY FROM relative_date WHERE d < CURRENT_DATE() + INTERVAL 3 YEAR ) 
            SELECT /*+ SET_VAR(cte_max_recursion_depth = 2K) */ 
                   d as `date`, 
                   LAST_DAY(d) = DATE(d) as `is_ultimo`, 
                   WEEKDAY(d) as `weekday`, 
                   DAYNAME(d) as `weekday_name` 
            from relative_date;

            CREATE VIEW %s AS SELECT * FROM %s WHERE weekday IN(0, 2, 5); 
SQL
                ,
                Level9::DATE_TABLE,
                Level9::VIEW_NAME_MONDAY_WEDNESDAY_SATURDAY,
                Level9::DATE_TABLE,
            )
        );
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
