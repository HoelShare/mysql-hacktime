<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210519193827 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            <<<'SQL'
            CREATE TABLE `settings`.`solution_try` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `number` INT NOT NULL,
              `user` VARCHAR(45) NOT NULL,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
              `success` TINYINT(1) NOT NULL DEFAULT 0,
              PRIMARY KEY (`id`),
              INDEX `user_level` (`user` ASC, `number` DESC));
SQL
        );
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
