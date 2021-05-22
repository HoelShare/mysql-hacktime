<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Level5;
use Doctrine\DBAL\Connection;

class Level5CreateView extends ViewCompareLevel
{
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
        return $this->validateView(
            $connection,
            Level5::EXPECTED_VIEW_NAME,
            Level5::TABLE_NAME_TO_COMPARE
        );
    }

    public function reset(Connection $connection): void
    {
        $connection->executeQuery('DROP TABLE IF EXISTS level5');
    }
}
