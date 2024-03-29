<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Globals;
use Doctrine\DBAL\Connection;

class Level18DynamicUltimo extends ViewCompareLevel
{
    private const TABLE_NAME = 'ultimo_date_range';

    public function create(Connection $connection): void
    {
        $connection->executeQuery(
            <<<'SQL'
CREATE TABLE `ultimo_date_range` (
   `ultimo` DATETIME NOT NULL,
   `month_start` DATETIME NOT NULL,
   `year_diff` int NOT NULL,
   `month_diff` int NOT NULL,
   `day_diff` int NOT null 
 );
SQL
        );

        $this->rootConnection->executeQuery(
            sprintf(
                <<<'SQL'
        INSERT INTO %s.%s 
SELECT * FROM
    ultimo
WHERE
    (ultimo.ultimo) IN (SELECT 
            MAX(ultimo) AS `ultimo`
        FROM
            ultimo UNION SELECT 
            MIN(ultimo)
        FROM
            ultimo)
SQL
                ,
                $connection->getDatabase(),
                self::TABLE_NAME
            )
        );
    }

    public function cleanUp(Connection $connection): void
    {
        $connection->executeQuery(
            sprintf(
                <<<'SQL'
            DROP TABLE IF EXISTS %s;
SQL
                ,
                self::TABLE_NAME
            ),
        );
    }

    public function getLevel(): int
    {
        return 18;
    }

    public function getDescription(): string
    {
        return sprintf(
            'Updating the ultimo view is an unnecessary recurring task. Build a dynamic Ultimo (Name: %s) view. Beside the ultimo column it is useful to have a year_diff, month_diff, day_diff column, which contain the offset to the current date. So we can Filter in Future Level on [month_diff > -36].
        Hint: To check your results and the structure of the view, I\'ve created a example Table which contain the expected first and last entry.
        Timerange: 6 Years in Back (+1 Month?) - 1 Year in Future (see: %s)
        
        The Ultimo View is needed in the following levels.',
            Globals::VIEW_NAME_ULTIMO,
            self::TABLE_NAME
        );
    }

    protected function getMainViewName(): string
    {
        return Globals::VIEW_NAME_ULTIMO;
    }

    public function validate(Connection $connection, string $username): ?string
    {
        return $this->checkResultSame($connection, Globals::VIEW_NAME_ULTIMO);
    }

    public function reset(Connection $connection): void
    {
        $this->cleanUp($connection);
    }
}
