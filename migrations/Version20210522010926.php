<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Level5;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210522010926 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
            CREATE TABLE %s (
                id int not null primary key, 
                name varchar(250) not null
            );
SQL
                ,
                Level5::TABLE_NAME_TO_COMPARE
            )
        );

        foreach (Level5::NAMES as $index => $name) {
            if ($name['test'] === 1) {
                continue;
            }

            $this->connection->executeQuery(
                'INSERT INTO ' . Level5::TABLE_NAME_TO_COMPARE . ' (id, name) VALUES (:id, :name)',
                ['id' => $index, 'name' => $name['name']]
            );
        }
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
