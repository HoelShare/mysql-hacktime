<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\InvalidUsernameException;
use App\Exception\UserAlreadyExists;
use Doctrine\DBAL\Connection;
use function in_array;
use function mb_strlen;

class UserService
{
    private const USERNAME_BLACKLIST = ['settings', 'root', 'sys', 'user', 'order', 'information_schema'];

    public function __construct(
        private Connection $connection,
        private DemoDataService $demoDataService,
    ) {
    }

    public function createUser(string $userName): string
    {
        $userName = mb_strtolower($userName);
        $this->checkUserExists($userName);
        $password = $this->randomPassword();

        $this->createInstance($userName, $password);

        return $password;
    }

    public function createInstance(string $name, string $password): void
    {
        $host = '%';
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
            DROP USER IF EXISTS '%s';
            DROP DATABASE IF EXISTS `%s`;
            CREATE USER '%s'@'%s' IDENTIFIED WITH caching_sha2_password BY '%s';
            GRANT USAGE ON *.* TO '%s'@'%s';
            CREATE DATABASE IF NOT EXISTS `%s`;
            GRANT ALL PRIVILEGES ON `%s`.* TO '%s'@'%s';
            INSERT INTO settings.user (username, password) VALUES (:username, :password);
SQL
                ,
                $name,
                $name,
                $name,
                $host,
                $password,
                $name,
                $host,
                $name,
                $name,
                $name,
                $host
            ),
            ['username' => $name, 'password' => $password]
        );

        $this->demoDataService->createDemoData($name, 0);
    }

    private function checkUserExists(string $userName): void
    {
        if (mb_strlen($userName) < 4) {
            throw new InvalidUsernameException(sprintf('Username (%s) too short', $userName));
        }

        if (preg_match('/\\W|_/', $userName) !== 0) {
            throw new InvalidUsernameException(sprintf('Username (%s) contains invalid characters', $userName));
        }

        if (in_array($userName, self::USERNAME_BLACKLIST, true)) {
            throw new InvalidUsernameException(sprintf('Username (%s) is on blacklist', $userName));
        }

        $user = $this->connection->fetchAssociative(
            'SELECT * FROM settings.user where username = :username',
            ['username' => $userName]
        );

        if ($user !== false) {
            throw new UserAlreadyExists($userName);
        }
    }

    private function randomPassword(): string
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = []; //remember to declare $pass as an array
        $alphaLength = mb_strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 16; $i++) {
            $n = random_int(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass); //turn the array into a string
    }

    public function isValid(string $user, mixed $password): bool
    {
        $result = $this->connection->fetchOne(
            'SELECT 1 FROM user where username = :user AND password = :password',
            ['user' => $user, 'password' => $password]
        );

        return $result !== false;
    }

    public function looseLevels(string $user): void
    {
        $this->connection->executeQuery(
            <<<'SQL'
            UPDATE solution_try SET success = 0 WHERE user = :user
SQL
            ,
            ['user' => $user],
        );
    }
}
