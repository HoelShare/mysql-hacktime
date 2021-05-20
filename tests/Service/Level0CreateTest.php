<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\UserService;

class Level0CreateTest extends KernelTestCase
{
    private UserService $userService;

    protected function setUp(): void
    {
        $this->userService = $this->containerInterface->get(UserService::class);
    }

    public function testCreate(): void
    {
        $this->connection->executeQuery(
            'DELETE FROM settings.user where username = :testUser',
            ['testUser' => self::TEST_USER]
        );
        $this->connection->executeQuery(
            'DELETE FROM settings.solution_try where user = :testUser',
            ['testUser' => self::TEST_USER]
        );
        $this->connection->executeQuery(
            'DROP DATABASE IF EXISTS `' . self::TEST_USER . '`;'
        );
        $this->connection->executeQuery(
            'DROP USER IF EXISTS `' . self::TEST_USER . '`;'
        );
        static::assertFalse(
            $this->connection->fetchOne(
                'SELECT SCHEMA_NAME from information_schema.SCHEMATA where SCHEMA_NAME = :test',
                ['test' => self::TEST_USER]
            )
        );
        $this->userService->createUser(self::TEST_USER);

        static::assertSame(
            self::TEST_USER,
            $this->connection->fetchOne(
                'SELECT SCHEMA_NAME from information_schema.SCHEMATA where SCHEMA_NAME = :test',
                ['test' => self::TEST_USER]
            )
        );
    }

    public function testCheckLevel0Succeed(): void
    {
        $this->connection->insert(self::TEST_USER . '.level', ['number' => 1]);

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}