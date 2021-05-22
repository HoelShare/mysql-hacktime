<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Constants\Globals;
use App\Constants\Level14;
use App\Constants\Level15;
use Doctrine\DBAL\Exception\DriverException;

class Level18DynamicUltimoTest extends KernelTestCase
{
    /**
     * @before
     */
    public function cleanUp()
    {
        static::markTestSkipped('Level 17 missing');
        try {
            $this->connection->executeQuery(
                sprintf(
                    'DROP VIEW IF EXISTS %s.`%s`;',
                    self::TEST_USER,
                    Globals::VIEW_NAME_ULTIMO,
                )
            );
        } catch (DriverException $exception) {
        }
    }

    public function testLevel16WrongData(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE view %s.`%s` as 
with recursive cte(ultimo) as (
SELECT 
    ADDTIME((LAST_DAY(NOW()) - INTERVAL 6 YEAR),
            '23:59:59')
    union
    select ADDTIME(CONVERT(LAST_DAY(cte.ultimo + INTERVAL 1 MONTH), DATETIME), '23:59:59') FROM cte where cte.ultimo < NOW() + INTERVAL 1 YEAR)
select ultimo, CONVERT(DATE(ultimo + INTERVAL 1 DAY - INTERVAL 1 MONTH), DATETIME) AS `month_start`, TIMESTAMPDIFF(YEAR, NOW(), ultimo) as `year_diff`, TIMESTAMPDIFF(MONTH, NOW(), ultimo) as `month_diff`, TIMESTAMPDIFF(DAY, NOW(), ultimo) as `day_diff` from cte;    
SQL
                ,
                self::TEST_USER,
                Globals::VIEW_NAME_ULTIMO,
            )
        );

        $this->assertView();
    }

    public function testLevel16WrongColumns(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE view %s.`%s` as 
with recursive cte(ultimo) as (
SELECT 
    ADDTIME((LAST_DAY(NOW()) - INTERVAL 6 YEAR - INTERVAL 1 MONTH),
            '23:59:59')
    union
    select ADDTIME(CONVERT(LAST_DAY(cte.ultimo + INTERVAL 1 MONTH), DATETIME), '23:59:59') FROM cte where cte.ultimo < NOW() + INTERVAL 1 YEAR)
select ultimo from cte;    
SQL
                ,
                self::TEST_USER,
                Globals::VIEW_NAME_ULTIMO,
            )
        );

        $this->assertView();
    }

    public function testLevel16Success(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE view %s.`%s` as 
with recursive cte(ultimo) as (
SELECT 
    ADDTIME((LAST_DAY(NOW()) - INTERVAL 6 YEAR - INTERVAL 1 MONTH),
            '23:59:59')
    union
    select ADDTIME(CONVERT(LAST_DAY(cte.ultimo + INTERVAL 1 MONTH), DATETIME), '23:59:59') FROM cte where cte.ultimo < NOW() + INTERVAL 1 YEAR)
select ultimo, CONVERT(DATE(ultimo + INTERVAL 1 DAY - INTERVAL 1 MONTH), DATETIME) AS `month_start`, TIMESTAMPDIFF(YEAR, NOW(), ultimo) as `year_diff`, TIMESTAMPDIFF(MONTH, NOW(), ultimo) as `month_diff`, TIMESTAMPDIFF(DAY, NOW(), ultimo) as `day_diff` from cte;    
SQL
                ,
                self::TEST_USER,
                Globals::VIEW_NAME_ULTIMO,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}