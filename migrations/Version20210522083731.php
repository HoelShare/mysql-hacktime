<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Faker\Factory;

final class Version20210522083731 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            <<<'SQL'
CREATE TABLE tenant (
    id INT NOT NULL PRIMARY KEY,
    name VARCHAR(250) NOT NULL
);

CREATE TABLE `order` (
    id INT NOT NULL,
    tenant_id INT NOT NULL,
    order_number VARCHAR(250) NOT NULL,
    currency VARCHAR(50) NOT NULL,
    `language` VARCHAR(50) NOT NULL,
    currency_factor NUMERIC(5 , 2 ) NOT NULL,
    sales_channel_id INT NOT NULL,
    billing_address_id INT NOT NULL,
    shipping_address_id INT NOT NULL,
    order_date_time DATETIME NOT NULL,
    amount_total NUMERIC(11 , 4 ) NOT NULL,
    amount_net NUMERIC(11 , 4 ) NOT NULL,
    shipping_total NUMERIC(11 , 4 ) NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id , tenant_id),
    FOREIGN KEY (tenant_id)
        REFERENCES tenant (id)
);

CREATE TABLE order_address (
    id INT NOT NULL,
    tenant_id INT NOT NULL,
    country VARCHAR(200) NOT NULL,
    order_id INT NOT NULL,
    company VARCHAR(250) NULL,
    department VARCHAR(250) NULL,
    salutation VARCHAR(50) NULL,
    title VARCHAR(250) NULL,
    first_name VARCHAR(250) NOT NULL,
    last_name VARCHAR(250) NOT NULL,
    street VARCHAR(250) NOT NULL,
    zipcode VARCHAR(250) NOT NULL,
    city VARCHAR(250) NOT NULL,
    tax_rate NUMERIC(4 , 2 ) NOT NULL,
    phone_number VARCHAR(250) NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id , tenant_id),
    FOREIGN KEY (order_id , tenant_id)
        REFERENCES `order` (id , tenant_id)
);

create table order_customer (
	id int not null,
    tenant_id int not null,
    customer_id int not null,
    order_id int not null,
    email varchar(250) not null,
    salutation varchar(250) null,
    first_name varchar(250) not null,
    last_name varchar(250) not null,
    title varchar(50) null,
    company varchar(250) null,
    created_at datetime not null,
    remote_address varchar(15) not null,
    primary key(id, tenant_id),
    foreign key(order_id, tenant_id) references `order`(id, tenant_id)
);

CREATE TABLE order_line_item (
    id INT NOT NULL,
    tenant_id INT NOT NULL,
    order_id INT NOT NULL,
    product_name VARCHAR(250) NOT NULL,
    quantity NUMERIC(10 , 3 ) NOT NULL,
    total_price NUMERIC(10 , 3 ) NOT NULL,
    good TINYINT(1) NOT NULL,
    position INT NOT NULL,
    PRIMARY KEY (id , tenant_id),
    FOREIGN KEY (order_id , tenant_id)
        REFERENCES `order` (id , tenant_id)
);
SQL
        );
        $faker = Factory::create();

        for ($tenant = 1; $tenant <= 15; $tenant++) {
            $this->connection->executeQuery(
                'INSERT INTO tenant (id, name) VALUES (:id, :name)',
                ['id' => $tenant, 'name' => $faker->userName()]
            );

            $prefix = $faker->word();
            $maxOrderCount = random_int(4, 10);
            $lineItemId = 1;

            for ($orderId = 1; $orderId <= $maxOrderCount; $orderId++) {
                $this->connection->executeQuery(
                    'INSERT INTO `order`
                    (
                        id,
                        tenant_id,
                        order_number,
                        currency,
                        language,
                        currency_factor,
                        sales_channel_id,
                        billing_address_id,
                        shipping_address_id,
                        order_date_time,
                        amount_total,
                        amount_net,
                        shipping_total,
                        created_at) VALUES (
:id,
:tenant_id,
:order_number,
:currency,
:language,
:currency_factor,
:sales_channel_id,
:billing_address_id,
:shipping_address_id,
:order_date_time,
:amount_total,
:amount_net,
:shipping_total,
:created_at
)',
                    [
                        'id' => $orderId,
                        'tenant_id' => $tenant,
                        'order_number' => sprintf(' % s % s', $prefix, $orderId),
                        'currency' => $faker->currencyCode(),
                        'language' => $faker->languageCode(),
                        'currency_factor' => $faker->randomFloat(2, 0, 5),
                        'sales_channel_id' => $faker->randomNumber(5),
                        'billing_address_id' => $faker->numberBetween(1, 4000),
                        'shipping_address_id' => $faker->numberBetween(1, 4000),
                        'order_date_time' => $faker->dateTime('-2 YEARS')->format(DATE_ATOM),
                        'amount_total' => $faker->randomFloat(2, 1, 1000),
                        'amount_net' => $faker->randomFloat(2, 1, 1000),
                        'shipping_total' => $faker->randomFloat(2, 1, 50),
                        'created_at' => (new \DateTime())->format(DATE_ATOM),
                    ]
                );

                $this->connection->executeQuery(
                    'INSERT INTO order_address(
id,
tenant_id,
country,
order_id,
company,
department,
salutation,
title,
first_name,
last_name,
street,
zipcode,
city,
tax_rate,
phone_number,
created_at
) VALUES (
:id,
:tenant_id,
:country,
:order_id,
:company,
:department,
:salutation,
:title,
:first_name,
:last_name,
:street,
:zipcode,
:city,
:tax_rate,
:phone_number,
:created_at
)',
                    [
                        'id' => $orderId,
                        'tenant_id' => $tenant,
                        'country' => $faker->country(),
                        'order_id' => $orderId,
                        'company' => $faker->company(),
                        'department' => $faker->jobTitle(),
                        'salutation' => $faker->boolean(70) ? null : $faker->slug(2),
                        'title' => $faker->boolean(70) ? null :$faker->title(),
                        'first_name' => $faker->firstName(),
                        'last_name' => $faker->lastName(),
                        'street' => $faker->streetName(),
                        'zipcode' => $faker->postcode(),
                        'city' => $faker->city(),
                        'tax_rate' => $faker->randomFloat(1, 6, 23),
                        'phone_number' => $faker->phoneNumber(),
                        'created_at' => (new \DateTime())->format(DATE_ATOM),
                    ]
                );

                $this->connection->executeQuery(
                    'INSERT INTO order_customer (
id,
tenant_id,
customer_id,
order_id,
email,
salutation,
first_name,
last_name,
title,
company,
created_at,
remote_address
) VALUES (
:id,
:tenant_id,
:customer_id,
:order_id,
:email,
:salutation,
:first_name,
:last_name,
:title,
:company,
:created_at,
:remote_address
)',
                    [
                        'id' => $orderId,
                        'tenant_id' => $tenant,
                        'customer_id' => $orderId,
                        'order_id' => $orderId,
                        'email' => $faker->companyEmail(),
                        'salutation' => $faker->boolean(70) ? null : $faker->slug(1),
                        'first_name' => $faker->firstName(),
                        'last_name' => $faker->lastName(),
                        'title' => $faker->boolean(70) ? null : $faker->title(),
                        'company' => $faker->company(),
                        'created_at' => $faker->dateTime('-2 YEARS')->format(DATE_ATOM),
                        'remote_address' => $faker->ipv4(),
                    ]
                );

                $numberPositions = random_int(1, 9);
                for ($position = 1; $position <= $numberPositions; $position++) {
                    $this->connection->executeQuery(
                        'INSERT INTO order_line_item (
id,
tenant_id,
order_id,
product_name,
quantity,
total_price,
good,
position
) VALUES (
:id,
:tenant_id,
:order_id,
:product_name,
:quantity,
:total_price,
:good,
:position
)',
                        [
                            'id' => $lineItemId++,
                            'tenant_id' => $tenant,
                            'order_id' => $orderId,
                            'product_name' => $faker->word(),
                            'quantity' => $faker->randomNumber(3),
                            'total_price' => $faker->randomFloat(2, 1, 1000),
                            'good' => (int) $faker->boolean(),
                            'position' => $lineItemId,
                        ]
                    );
                }
            }
        }
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
