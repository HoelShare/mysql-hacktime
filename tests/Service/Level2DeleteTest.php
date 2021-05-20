<?php declare(strict_types=1);

namespace App\Tests\Service;

class Level2DeleteTest extends KernelTestCase
{
    public function testLevel2ErrorPartDeleted(): void
    {
        $this->connection->executeQuery('DELETE FROM ' . self::TEST_USER . '.level2 limit 2');

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame('You need to delete all rows!', $error);
    }

    public function testLevel2Succeed(): void
    {
        $this->connection->executeQuery('DELETE FROM ' . self::TEST_USER . '.level2');

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}