<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Level5;
use Doctrine\DBAL\Connection;
use Faker\Factory;

class Level5CreateView implements DemoDataInterface
{
    public function __construct(
        private Connection $rootConnection,
    ) {
    }

    public function create(Connection $connection): void
    {
        $connection->executeQuery(
            <<<'SQL'
            DROP TABLE IF EXISTS level5;
            CREATE TABLE level5 (
                id int not null,
                name varchar(250) not null,
                test tinyint(1) not null default '0',
             primary key(id));
SQL
        );

        foreach (Level5::NAMES as $index => $name) {
            $connection->executeQuery(
                'INSERT INTO level5 (id, name, test) VALUES (:id, :name, :test)',
                ['id' => $index, 'name' => $name['name'], 'test' => $name['test']]
            );
        }
    }

    public function cleanUp(Connection $connection): void
    {
        $connection->executeQuery('DROP TABLE IF EXISTS level5');
    }

    public function getLevel(): int
    {
        return 5;
    }

    public function getDescription(): string
    {
        return sprintf(
            'Create a view (%s) which contains all users which are not test users. Columns: [id, name]',
            Level5::EXPECTED_VIEW_NAME,
        );
    }

    public function validate(Connection $connection, string $username): ?string
    {
        $baseType = $connection->fetchOne(
            'SELECT TABLE_TYPE from information_schema.tables where TABLE_SCHEMA = :userSchema and TABLE_NAME = :table',
            [
                'userSchema' => $username,
                'table' => Level5::EXPECTED_VIEW_NAME,
            ]
        );

        if ($baseType === false) {
            return 'Did you try to create the view and just used a wrong name?';
        }

        if ($baseType !== 'VIEW') {
            return 'It does not look like a view?';
        }

        $rows = $connection
            ->createQueryBuilder()
            ->select('*')
            ->from(Level5::EXPECTED_VIEW_NAME)
            ->execute()
            ->fetchAllAssociative();

        $compareRows = $this->rootConnection
            ->createQueryBuilder()
            ->select('*')
            ->from(Level5::TABLE_NAME_TO_COMPARE)
            ->execute()
            ->fetchAllAssociative();

        if ($compareRows !== $rows) {
            return 'The result seems to be wrong, possible mistakes. The view contains invalid data or wrong column names.';
        }

        return null;
    }

    public function reset(Connection $connection): void
    {
        $this->cleanUp($connection);
    }
}