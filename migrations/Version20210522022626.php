<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Constants\Level6;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210522022626 extends AbstractMigration
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
                Level6::TABLE_NAME_TO_COMPARE
            )
        );

        foreach (Level6::PERSONS as $index => $person) {
            if ($person['type'] !== Level6::TYPE_TEACHER) {
                continue;
            }
            $person['id'] = $index;
            unset($person['type']);

            $this->connection->insert(Level6::TABLE_NAME_TO_COMPARE, $person);
        }
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
