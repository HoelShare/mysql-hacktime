<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Level6;
use Doctrine\DBAL\Connection;

class Level6ReCreateView extends ViewCompareLevel
{
    public const TABLE_NAME = 'hogwarts_person';

    public function create(Connection $connection): void
    {
        $connection->executeQuery(
            sprintf(
                <<<'SQL'
            DROP TABLE IF EXISTS %s;
            CREATE TABLE %s (
                id int not null,
                name varchar(250) not null,
                type varchar(45) not null,
             primary key(id));
SQL
                ,
                self::TABLE_NAME,
                self::TABLE_NAME
            )
        );

        $connection->executeQuery(
            sprintf(
                <<<'SQL'
        DROP VIEW IF EXISTS %s;
        CREATE VIEW %s as SELECT id, name FROM %s
SQL
                ,
                Level6::EXPECTED_VIEW_NAME,
                Level6::EXPECTED_VIEW_NAME,
                self::TABLE_NAME
            )
        );

        foreach (Level6::PERSONS as $index => $person) {
            $person['id'] = $index;
            $connection->insert(self::TABLE_NAME, $person);
        }
    }

    public function cleanUp(Connection $connection): void
    {
        $connection->executeQuery('DROP TABLE IF EXISTS ' . self::TABLE_NAME);
        $connection->executeQuery('DROP VIEW IF EXISTS ' . Level6::EXPECTED_VIEW_NAME);
    }

    public function getLevel(): int
    {
        return 6;
    }

    public function getDescription(): string
    {
        return sprintf(
            'Due to a mistake %s does not contain the Hogwarts teachers. Please fix this!',
            Level6::EXPECTED_VIEW_NAME,
        );
    }

    public function validate(Connection $connection, string $username): ?string
    {
        return $this->validateView(
            $connection,
            $username,
            Level6::EXPECTED_VIEW_NAME,
            Level6::TABLE_NAME_TO_COMPARE
        );
    }

    public function reset(Connection $connection): void
    {
        $this->cleanUp($connection);
    }
}