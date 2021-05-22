<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\UserNotFoundException;
use Doctrine\DBAL\Connection;

class ConnectionFactory
{
    private array $connections = [];

    public function __construct(
        private Connection $connection,
    ) {
    }

    private function getPassword(string $username): string
    {
        $password = $this->connection->fetchOne(
            'SELECT password FROM settings.user where username = :username',
            ['username' => $username]
        );
        if ($password === false) {
            throw new UserNotFoundException($username);
        }

        return (string) $password;
    }

    public function createForUser(string $username): Connection
    {
        if (isset($this->connections[$username])) {
            return $this->connections[$username];
        }

        $password = $this->getPassword($username);
        $params = array_merge(
            $this->connection->getParams(),
            ['user' => $username, 'password' => $password, 'dbname' => $username]
        );
        unset($params['url']);
        $connection = new Connection($params, $this->connection->getDriver());
        $this->connections[$username] = $connection;

        return $connection;
    }
}
