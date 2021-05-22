<?php
declare(strict_types=1);

namespace App\Tests\Service;

class Level01UpdateTest extends KernelTestCase
{
    public function testLevel1Succeed(): void
    {
        $this->connection->executeQuery('UPDATE ' . self::TEST_USER . '.level set solution = 2 where number = 1');

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}