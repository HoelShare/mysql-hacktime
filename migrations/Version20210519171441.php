<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210519171441 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(<<<'SQL'
        CREATE TABLE `settings`.`user` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `username` VARCHAR(45) NOT NULL,
          `password` VARCHAR(45) NOT NULL,
          `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
          PRIMARY KEY (`id`));
SQL
        );
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
