<?php
declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;

class JokeService
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function getRandom(): array
    {
        $jokes = $this->connection->fetchAllAssociative('SELECT * FROM joke');

        shuffle($jokes);

        return array_pop($jokes);
    }
}
