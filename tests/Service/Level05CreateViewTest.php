<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Constants\Level5;
use Doctrine\DBAL\Exception\DriverException;

class Level05CreateViewTest extends KernelTestCase
{
    /**
     * @after
     */
    public function cleanUp()
    {
        try {
            $this->connection->executeQuery(
                'DROP VIEW IF EXISTS ' . self::TEST_USER . '.' . Level5::EXPECTED_VIEW_NAME
            );
        } catch (DriverException $exception) {
        }
        try {
            $this->connection->executeQuery(
                'DROP TABLE IF EXISTS ' . self::TEST_USER . '.' . Level5::EXPECTED_VIEW_NAME
            );
        } catch (DriverException $exception) {
        }
    }

    public function testLevel5ViewNotFound(): void
    {
        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame('Did you try to create the view and just used a wrong name?', $error);
    }

    public function testLevel5NotAView(): void
    {
        $this->connection->executeQuery(
            'CREATE TABLE ' . self::TEST_USER . '.' . Level5::EXPECTED_VIEW_NAME . '(id int not null, name varchar(250));'
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame('It does not look like a view?', $error);
    }

    public function testLevel5WrongFilter(): void
    {
        $this->connection->executeQuery(
            'CREATE VIEW ' . self::TEST_USER . '.' . Level5::EXPECTED_VIEW_NAME . ' AS SELECT id, name FROM ' . self::TEST_USER . '.level5'
        );

        $this->assertView();
    }

    public function testLevel5WrongColumns(): void
    {
        $this->connection->executeQuery(
            'CREATE VIEW ' . self::TEST_USER . '.' . Level5::EXPECTED_VIEW_NAME . ' AS SELECT id, name, test FROM ' . self::TEST_USER . '.level5 WHERE test = 0'
        );

        $this->assertView();
    }


    public function testLevel5Success(): void
    {
        $this->connection->executeQuery(
            'CREATE VIEW ' . self::TEST_USER . '.' . Level5::EXPECTED_VIEW_NAME . ' AS SELECT id, name FROM ' . self::TEST_USER . '.level5 WHERE test = 0'
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}