<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Constants\Globals;
use App\Constants\Level14;
use App\Constants\Level15;
use App\Constants\Level16;
use App\Constants\Level19;
use App\Constants\Level21;
use Doctrine\DBAL\Exception\DriverException;

class Level21JsonExtractionTest extends KernelTestCase
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
                    Level21::EXPECTED_VIEW_NAME,
                )
            );
        } catch (DriverException $exception) {
        }
    }

    public function testLevel21WrongFilter(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
CREATE VIEW %s.%s
AS
SELECT 
    id, tenant_id, created_at, JSON_UNQUOTE(data->"$.keyword") as `keyword`
FROM
    %s
WHERE
    type = 'sea';    
SQL
                ,
                self::TEST_USER,
                Level21::EXPECTED_VIEW_NAME,
                Globals::TABLE_EVENT_LOG,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame(
            'The result seems to be wrong, did you filter the expected?',
            $error
        );
    }

    public function testLevel21WrongColumns(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
CREATE VIEW %s.%s
AS
SELECT 
    id, created_at, JSON_UNQUOTE(data->"$.keyword") as `keyword`
FROM
    %s
WHERE
    type = 'search';    
SQL
                ,
                self::TEST_USER,
                Level21::EXPECTED_VIEW_NAME,
                Globals::TABLE_EVENT_LOG,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame(
            'Did you name the columns as expected?
            id,tenant_id,created_at,keyword',
            $error
        );
    }


    public function testLevel21Success(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
CREATE VIEW %s.%s
AS
SELECT 
    id, tenant_id, created_at, JSON_UNQUOTE(data->"$.keyword") as `keyword`
FROM
    %s
WHERE
    type = 'search';    
SQL
                ,
                self::TEST_USER,
                Level21::EXPECTED_VIEW_NAME,
                Globals::TABLE_EVENT_LOG,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}