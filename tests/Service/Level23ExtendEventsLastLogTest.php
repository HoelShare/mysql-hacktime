<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Constants\Globals;
use App\Constants\Level14;
use App\Constants\Level15;
use App\Constants\Level16;
use App\Constants\Level19;
use App\Constants\Level21;
use App\Constants\Level22;
use Doctrine\DBAL\Exception\DriverException;

class Level23ExtendEventsLastLogTest extends KernelTestCase
{
    /**
     * @before
     */
    public function cleanUp()
    {
        try {
            $this->connection->executeQuery(
                sprintf(
                    'DROP VIEW IF EXISTS %s.`%s`;',
                    self::TEST_USER,
                    Level22::EXPECTED_VIEW_NAME,
                )
            );
        } catch (DriverException $exception) {
        }
    }

    public function testLevel23WrongFilter(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
CREATE DEFINER='%s'@'%s' VIEW %s.%s AS
SELECT 
    event_log.*,
    to_days(created_at) - to_days(first_value(created_at) over w) as `days_since_first_log`,
    COALESCE(to_days(created_at) - TO_DAYS(LAG(created_at) over w), 0) as `days_since_last_log`
FROM
    %s.%s event_log
    WHERE tenant_id = 1
    window w as (partition by tenant_id order by created_at)
SQL
                ,
                self::TEST_USER,
                '%',
                self::TEST_USER,
                Level22::EXPECTED_VIEW_NAME,
                self::TEST_USER,
                Globals::TABLE_EVENT_LOG,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame(
            'The result seems to be wrong, did you filter the expected?',
            $error
        );
    }

    public function testLevel22WrongColumns(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'

CREATE DEFINER='%s'@'%s' VIEW %s.%s AS
SELECT 
    event_log.id,
    to_days(created_at) - to_days(first_value(created_at) over w) as `days_since_first_log`,
    COALESCE(to_days(created_at) - TO_DAYS(LAG(created_at) over w), 0) as `days_since_last_log`
FROM
    %s.%s event_log
    window w as (partition by tenant_id order by created_at)
SQL
                ,
                self::TEST_USER,
                '%',
                self::TEST_USER,
                Level22::EXPECTED_VIEW_NAME,
                self::TEST_USER,
                Globals::TABLE_EVENT_LOG,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame(
            'Did you name the columns as expected?
            id,tenant_id,created_at,type,data,days_since_first_log,days_since_last_log',
            $error
        );
    }

    public function testLevel23FilterWithoutWindow(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
CREATE DEFINER='%s'@'%s' VIEW %s.%s AS
SELECT 
    event_log.*,
    TO_DAYS(created_at) - TO_DAYS((SELECT 
                    MIN(i.created_at)
                FROM
                    %s.%s i
                WHERE
                    i.tenant_id = event_log.tenant_id)) AS `days_since_first_log`,
    COALESCE(TO_DAYS(created_at) - TO_DAYS((SELECT 
                            MAX(i.created_at)
                        FROM
                            %s.%s i
                        WHERE
                            i.tenant_id = event_log.tenant_id
                                AND i.created_at < event_log.created_at)),
            0) AS `days_since_last_log`
FROM
    %s.%s event_log
ORDER BY tenant_id , days_since_first_log
SQL
                ,
                self::TEST_USER,
                '%',
                self::TEST_USER,
                Level22::EXPECTED_VIEW_NAME,
                self::TEST_USER,
                Globals::TABLE_EVENT_LOG,
                self::TEST_USER,
                Globals::TABLE_EVENT_LOG,
                self::TEST_USER,
                Globals::TABLE_EVENT_LOG,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame(
            'For performance reasons a WINDOW Function should be used!',
            $error
        );
    }

    public function testLevel23Success(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
CREATE DEFINER='%s'@'%s' VIEW %s.%s AS
SELECT 
    event_log.*,
    to_days(created_at) - to_days(first_value(created_at) over w) as `days_since_first_log`,
    COALESCE(to_days(created_at) - TO_DAYS(LAG(created_at) over w), 0) as `days_since_last_log`
FROM
    %s.%s event_log
    window w as (partition by tenant_id order by created_at)
SQL
                ,
                self::TEST_USER,
                '%',
                self::TEST_USER,
                Level22::EXPECTED_VIEW_NAME,
                self::TEST_USER,
                Globals::TABLE_EVENT_LOG,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}