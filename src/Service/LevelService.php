<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\UserNotFoundException;
use App\Service\DemoData\DemoDataInterface;
use Doctrine\DBAL\Connection;

class LevelService
{
    public function __construct(
        private Connection $connection,
        private DemoDataService $demoDataService,
    ) {
    }

    public function checkMax(string $username): ?string
    {
        $currentLevel = $this->getCurrentLevel($username);

        $success = $this->demoDataService->checkLevel($username, $currentLevel->getLevel());

        $this->logTry($username, $currentLevel->getLevel(), $success === null);

        return $success;
    }

    private function checkUserExists(string $username): void
    {
        $user = $this->connection->fetchOne(
            'SELECT * FROM settings.user where username = :username',
            ['username' => $username]
        );

        if ($user === false) {
            throw new UserNotFoundException($username);
        }
    }

    public function getCurrentLevel(string $username): DemoDataInterface
    {
        $this->checkUserExists($username);
        $level = $this->connection->fetchOne(
            'SELECT MAX(number) FROM solution_try where success = true and user = :username',
            ['username' => $username]
        );
        if ($level === false || $level === null) {
            return $this->demoDataService->getLevel(0);
        }

        return $this->demoDataService->getLevel(1 + (int) $level);
    }

    private function logTry(string $username, int $currentLevel, bool $success): void
    {
        $this->connection->executeQuery(
            <<<'SQL'
            INSERT INTO solution_try (number, user, success) VALUES (:number, :user, :success)
SQL
            ,
            ['number' => $currentLevel, 'user' => $username, 'success' => (int) $success]
        );
    }

    public function getLevelTry(string $user, int $currentLevel): int
    {
        $try = $this->connection->fetchOne(
            'SELECT count(*) FROM solution_try WHERE number = :level and user = :user',
            ['level' => $currentLevel, 'user' => $user]
        );

        if ($try === false) {
            return 0;
        }

        return (int) $try;
    }
}
