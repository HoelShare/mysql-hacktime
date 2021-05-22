<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Level6;
use App\Constants\Level7;
use App\Constants\Level8;
use App\Constants\Level9;
use Doctrine\DBAL\Connection;

class Level9In extends ViewCompareLevel
{
    public function create(Connection $connection): void
    {
        $connection->executeQuery(
            <<<'SQL'
            CREATE TABLE `date_table` (
               `date` date NOT NULL,
               `is_ultimo` int NOT NULL,
               `weekday` int NOT NULL,
               `weekday_name` varchar(9) NOT NULL,
               primary key (`date`)
             )
SQL
        );

        $this->rootConnection->executeQuery(
            sprintf(
                <<<'SQL'
            INSERT INTO %s.date_table SELECT * FROM date_table
SQL
                ,
                $connection->getDatabase()
            )
        );
    }

    public function cleanUp(Connection $connection): void
    {
    }

    public function getLevel(): int
    {
        return 9;
    }

    public function getDescription(): string
    {
        return sprintf(
            'A recurring training of hacktime.de, which is held 3 times a week (Monday, Wednesday and Saturday) need a list (%s) of dates.',
            Level9::VIEW_NAME_MONDAY_WEDNESDAY_SATURDAY,
        );
    }

    public function validate(Connection $connection, string $username): ?string
    {
        $viewResponse = $this->validateView(
            $connection,
            Level9::VIEW_NAME_MONDAY_WEDNESDAY_SATURDAY,
            Level9::VIEW_NAME_MONDAY_WEDNESDAY_SATURDAY,
        );
        if ($viewResponse !== null) {
            return $viewResponse;
        }

        $definition = $this->getViewDefinition($connection, Level9::VIEW_NAME_MONDAY_WEDNESDAY_SATURDAY);

        if (stripos($definition, ' in') === false) {
            return 'Try filtering with IN. [weekday IN (0, 2, 5)]';
        }

        return null;
    }

    public function reset(Connection $connection): void
    {
        $connection->executeQuery(
            <<<'SQL'
            DROP TABLE IF EXISTS star_wars_ship_pilot;
            DROP TABLE IF EXISTS star_wars_star_ship;
            DROP TABLE IF EXISTS star_wars_character;
SQL
        );
    }
}