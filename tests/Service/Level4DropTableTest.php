<?php
declare(strict_types=1);

namespace App\Tests\Service;

class Level4DropTableTest extends KernelTestCase
{
    /**
     * @before
     */
    public function cleanUp(): void
    {
        $this->connection->executeQuery('CREATE TABLE IF NOT EXISTS ' . self::TEST_USER . '.level4 (id int not null primary key)');
    }

    public function testLevel4TableNotDeleted(): void
    {
        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame('The sensitive data still exist. Did you really remove the table?', $error);
    }

    public function testLevel4Success(): void
    {
        $this->connection->executeQuery('DROP TABLE ' . self::TEST_USER . '.level4');

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}