<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use Doctrine\DBAL\Connection;
use Faker\Factory;

class Level4DropTable implements DemoDataInterface
{
    public const EXPECTED_TABLE_NAME = 'level4';

    public function create(Connection $connection): void
    {
        $faker = Factory::create();
        $connection->executeQuery(
            <<<'SQL'
            DROP TABLE IF EXISTS level4;
            CREATE TABLE level4 (
                id int auto_increment not null,
                name varchar(250) not null,
             primary key(id));
SQL
        );

        for ($i = 0; $i < 10; $i++) {
            $connection->executeQuery('INSERT INTO level4 (name) VALUES (:name)', ['name' => $faker->name()]);
        }
    }

    public function cleanUp(Connection $connection): void
    {
    }

    public function getLevel(): int
    {
        return 4;
    }

    public function getDescription(): string
    {
        return sprintf(
            'There is a Table ("%s"), which contains very sensitive data. This data is such sensitive that we need to remove the table instead of the content.',
            self::EXPECTED_TABLE_NAME
        );
    }

    public function validate(Connection $connection, string $username): ?string
    {
        $baseType = $connection->fetchOne(
            'SELECT TABLE_TYPE from information_schema.tables where TABLE_SCHEMA = :userSchema and TABLE_NAME = :table',
            [
                'userSchema' => $username,
                'table' => self::EXPECTED_TABLE_NAME,
            ]
        );

        if ($baseType !== false) {
            return 'The sensitive data still exist. Did you really remove the table?';
        }

        return null;
    }

    public function reset(Connection $connection): void
    {
        $connection->executeQuery('DROP TABLE IF EXISTS level4');
    }
}