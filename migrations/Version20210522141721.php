<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Faker\Factory;
use Faker\Generator;

final class Version20210522141721 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            <<<'SQL'
            create table event_log(
                id int not null,
                tenant_id int not null,
                created_at datetime not null,
                type varchar(250) not null,
                data JSON null,
                primary key (id, tenant_id),
                foreign key (tenant_id) references tenant (id)
            );
SQL
        );

        $faker = Factory::create();

        $tenants = $this->connection->fetchFirstColumn('SELECT id from tenant');

        $types = [
            'change_theme',
            'create_product',
            'update_product',
            'login',
            'search',
            'payment_change',
            'snippet_change',
            'test_order',
        ];

        foreach ($tenants as $tenantId) {
            $maxEvents = $faker->randomNumber(2);

            for ($eventId = 1; $eventId <= $maxEvents; $eventId++) {
                $type = $faker->randomElement($types);
                $createdAt = $faker->dateTimeBetween('-1 years');
                $data = null;
                if (method_exists($this, $type)) {
                    $data = $this->$type($faker);
                }

                $this->connection->executeQuery(
                    'INSERT INTO event_log (id, tenant_id, created_at, type, data) VALUES (:id, :tenant, :created, :type, :data)',
                    [
                        'id' => $eventId,
                        'tenant' => $tenantId,
                        'created' => $createdAt->format(DATE_ATOM),
                        'type' => $type,
                        'data' => $data,
                    ]
                );
            }
        }
    }

    public function change_theme(Generator $faker): string
    {
        $data = [
            'theme_id' => $faker->randomNumber(4),
            'theme_name' => $faker->domainName(),
        ];

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    public function create_product(Generator $faker): string
    {
        $data = [
            'product_id' => $faker->randomNumber(4),
            'product_name' => $faker->word(),
            'price' => $faker->randomFloat(2, 5, 200),
        ];

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    public function update_product(Generator $faker): string
    {
        return $this->create_product($faker);
    }

    public function search(Generator $faker): string
    {
        $data = [
            'keyword' => $faker->sentence(3, true),
        ];

        return json_encode($data, JSON_THROW_ON_ERROR);
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
