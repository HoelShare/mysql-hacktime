<?php
declare(strict_types=1);

namespace App\Tests\Service;

class Level3CreateTableTest extends KernelTestCase
{
    /**
     * @before
     */
    public function cleanUp(): void
    {
        $this->connection->executeQuery('DROP TABLE IF EXISTS ' . self::TEST_USER . '.level3');
        $this->connection->executeQuery('DROP VIEW IF EXISTS ' . self::TEST_USER . '.level3');
    }

    public function testLevel3TableNotFound(): void
    {
        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame('No Table with the expected Name found. Did you use the correct name?', $error);
    }

    public function testLevel3NotATable(): void
    {
        $this->connection->executeQuery('CREATE VIEW ' . self::TEST_USER . '.level3 AS SELECT 1');

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame('Did you define it as a Table?', $error);

        $this->connection->executeQuery('DROP VIEW IF EXISTS ' . self::TEST_USER . '.level3');
    }

    public function testLevel3NotIdColumn(): void
    {
        $this->connection->executeQuery('CREATE TABLE ' . self::TEST_USER . '.level3 AS SELECT 1');

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame('Did you name the first column as expected?', $error);

        $this->connection->executeQuery('DROP TABLE IF EXISTS ' . self::TEST_USER . '.level3');
    }

    public function testLevel3NotIdColumnPrimary(): void
    {
        $this->connection->executeQuery('CREATE TABLE ' . self::TEST_USER . '.level3 AS SELECT 1 as id');

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame('Did you create the column with a Primary Key?', $error);

        $this->connection->executeQuery('DROP TABLE IF EXISTS ' . self::TEST_USER . '.level3');
    }

    public function testLevel3Success(): void
    {
        $this->connection->executeQuery('CREATE TABLE ' . self::TEST_USER . '.level3 (id int not null primary key)');

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}