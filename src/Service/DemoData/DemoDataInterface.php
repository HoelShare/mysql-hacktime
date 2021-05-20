<?php declare(strict_types=1);

namespace App\Service\DemoData;

use Doctrine\DBAL\Connection;

interface DemoDataInterface
{
    public function create(Connection $connection): void;

    public function cleanUp(Connection $connection): void;

    public function getLevel(): int;

    public function getDescription(): string;

    public function validate(Connection $connection, string $username): ?string;

    public function reset(Connection $connection): void;
}