<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Constants\Level5;
use App\Constants\Level6;
use App\Service\DemoData\Level6ReCreateView;
use Doctrine\DBAL\Exception\DriverException;

class Level6ReCreateViewTest extends KernelTestCase
{
    /**
     * @after
     */
    public function cleanUp()
    {
        try {
            $this->connection->executeQuery(
                sprintf(
                    'CREATE OR REPLACE DEFINER=%s@%s VIEW %s.%s AS SELECT id, name FROM %s.%s',
                    self::TEST_USER,
                    '\'%\'',
                    self::TEST_USER,
                    Level6::EXPECTED_VIEW_NAME,
                    self::TEST_USER,
                    Level6ReCreateView::TABLE_NAME,
                )
            );
        } catch (DriverException $exception) {
        }
    }

    public function testLevel6WrongFilter(): void
    {
        $this->connection->executeQuery(
            sprintf(
                'CREATE OR REPLACE DEFINER=%s@%s VIEW %s.%s AS SELECT id, name FROM %s.%s WHERE type = :type',
                self::TEST_USER,
                '\'%\'',
                self::TEST_USER,
                Level6::EXPECTED_VIEW_NAME,
                self::TEST_USER,
                Level6ReCreateView::TABLE_NAME,
            ),
            ['type' => Level6::TYPE_STUDENT]
        );

        $this->assertView();
    }

    public function testLevel6WrongColumns(): void
    {
        $this->connection->executeQuery(
            sprintf(
                'CREATE OR REPLACE DEFINER=%s@%s VIEW %s.%s AS SELECT id, name, type FROM %s.%s WHERE type = :type',
                self::TEST_USER,
                '\'%\'',
                self::TEST_USER,
                Level6::EXPECTED_VIEW_NAME,
                self::TEST_USER,
                Level6ReCreateView::TABLE_NAME,
            ),
            ['type' => Level6::TYPE_TEACHER]
        );

        $this->assertView();
    }


    public function testLevel6Success(): void
    {
        $this->connection->executeQuery(
            sprintf(
                'CREATE OR REPLACE DEFINER=%s@%s VIEW %s.%s AS SELECT id, name FROM %s.%s WHERE type = :type',
                self::TEST_USER,
                '\'%\'',
                self::TEST_USER,
                Level6::EXPECTED_VIEW_NAME,
                self::TEST_USER,
                Level6ReCreateView::TABLE_NAME,
            ),
            ['type' => Level6::TYPE_TEACHER]
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}