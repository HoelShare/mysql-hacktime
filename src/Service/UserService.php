<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\InvalidUsernameException;
use Doctrine\DBAL\Connection;

class UserService
{
    private const USERNAME_BLACKLIST = ['settings', 'root'];

    public function __construct(
        private Connection $connection,
        private DemoDataService $demoDataService,
    ) {
    }

    public function createUser(string $userName): string
    {
        $userName = strtolower($userName);
        $this->checkUserExists($userName);
        $password = $this->randomPassword();

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
            INSERT INTO settings.users (username, password) VALUES (:username, :password);
SQL
                ,
                $userName,
                $userName,
                $userName,
                $host,
                $password,
                $userName,
                $host,
                $userName,
                $userName,
                $userName,
                $host
            ),
            ['username' => $userName, 'password' => $password,]
        );

        $this->demoDataService->createDemoData($userName, 0);

        return $password;
    }

    private function checkUserExists(string $userName): void
    {
        if (strlen($userName) < 4) {
            throw new InvalidUsernameException(sprintf('Username (%s) too short', $userName));
        }

        if (preg_match('/\\W/', $userName) !== 0) {
            throw new InvalidUsernameException(sprintf('Username (%s) contains invalid characters', $userName));
        }

        if (in_array($userName, self::USERNAME_BLACKLIST)) {
            throw new InvalidUsernameException(sprintf('Username (%s) is on blacklist', $userName));
        }

        $user = $this->connection->fetchAssociative(
            'SELECT * FROM settings.users where username = :username',
            ['username' => $userName]
        );

        if ($user !== false) {
            throw new InvalidUsernameException(sprintf('Username (%s) already exists', $userName));
        }
    }

    private function randomPassword(): string
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 16; $i++) {
            $n = random_int(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}