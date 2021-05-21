<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use Doctrine\DBAL\Connection;
use Faker\Factory;

class Level3CreateTable implements DemoDataInterface
{
    public const EXPECTED_TABLE_NAME = 'level3';
    public const EXPECTED_FIRST_COLUMN_NAME = 'id';

    public function create(Connection $connection): void
    {
        $connection->executeQuery(
            <<<'SQL'
            DROP TABLE IF EXISTS level3;
SQL
        );
    }

    public function cleanUp(Connection $connection): void
    {
        $connection->executeQuery('DROP TABLE IF EXISTS level3');
        $connection->executeQuery('DROP VIEW IF EXISTS level3');
    }

    public function getLevel(): int
    {
        return 3;
    }

    public function getDescription(): string
    {
        return sprintf(
            'Create a Table with the name "%s", it should contain at least an ID column, ' .
            'which should be the first column and is a primary key.',
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

        if ($baseType === false) {
            return 'No Table with the expected Name found. Did you use the correct name?';
        }

        if ($baseType !== 'BASE TABLE') {
            return 'Did you define it as a Table?';
        }

        $column = $connection->fetchAssociative(
            'SELECT COLUMN_NAME, COLUMN_KEY 
                    from information_schema.COLUMNS 
                    where TABLE_SCHEMA = :userSchema and TABLE_NAME = :tableName 
                    ORDER BY ORDINAL_POSITION ',
            ['userSchema' => $username, 'tableName' => self::EXPECTED_TABLE_NAME]
        );

        if (strtolower($column['COLUMN_NAME']) !== self::EXPECTED_FIRST_COLUMN_NAME) {
            return 'Did you name the first column as expected?';
        }

        if (strtolower($column['COLUMN_KEY']) !== 'pri') {
            return 'Did you create the column with a Primary Key?';
        }

        return null;
    }

    public function reset(Connection $connection): void
    {
        $this->cleanUp($connection);
    }
}