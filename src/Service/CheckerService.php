<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\UserNotFoundException;
use Doctrine\DBAL\Connection;

class CheckerService
{
    public function __construct(
        private Connection $connection,
        private DemoDataService $demoDataService,
    ) {
    }

    public function checkMax(string $username): bool
    {
        $currentLevel = $this->getCurrentLevel($username);

        $success = $this->demoDataService->checkLevel($username, $currentLevel);

        $this->logTry($username, $currentLevel, $success);

        return $success;
    }

    private function checkUserExists(string $username): void
    {
        $user = $this->connection->fetchOne(
            'SELECT * FROM settings.users where username = :username',
            ['username' => $username]
        );

        if ($user === false) {
            throw new UserNotFoundException($username);
        }
    }

    public function getCurrentLevel(string $username): int
    {
        $this->checkUserExists($username);
        $level = $this->connection->fetchOne(
            'SELECT MAX(number) FROM solution_try where success = true and user = :username',
            ['username' => $username]
        );
        if ($level === false || $level === null) {
            return 0;
        }

        return 1 + (int)$level;
    }

    private function logTry(string $username, int $currentLevel, bool $success): void
    {
        $this->connection->executeQuery(
            <<<'SQL'
            INSERT INTO solution_try (number, user, success) VALUES (:number, :user, :success)
SQL
            ,
            ['number' => $currentLevel, 'user' => $username, 'success' => (int)$success]
        );
    }
}