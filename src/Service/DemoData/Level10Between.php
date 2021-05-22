<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Level10;
use Doctrine\DBAL\Connection;

class Level10Between extends ViewCompareLevel
{
    public function create(Connection $connection): void
    {
        $connection->executeQuery(
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

        foreach (['tenant', '`order`', 'order_address', 'order_customer', 'order_line_item'] as $table) {
            $this->rootConnection->executeQuery(
                sprintf(
                    'INSERT INTO %s.%s SELECT * FROM %s',
                    $connection->getDatabase(),
                    $table,
                    $table,
                )
            );
        }
    }

    public function cleanUp(Connection $connection): void
    {
    }

    public function getLevel(): int
    {
        return 10;
    }

    public function getDescription(): string
    {
        return sprintf(
            'We will focus on some mid market orders. Therefore we need a list (%s) with all orders which have a total amount greater than 150 and less then 500.',
            Level10::VIEW_NAME_MID_MARKET_ORDERS,
        );
    }

    public function validate(Connection $connection, string $username): ?string
    {
        $viewResponse = $this->validateView(
            $connection,
            Level10::VIEW_NAME_MID_MARKET_ORDERS,
            Level10::VIEW_NAME_MID_MARKET_ORDERS,
        );

        if ($viewResponse !== null) {
            return $viewResponse;
        }

        $definition = $this->getViewDefinition($connection, Level10::VIEW_NAME_MID_MARKET_ORDERS);

        if (mb_stripos($definition, ' between') === false) {
            return 'Try filtering with BETWEEN, so you do not need >= and <=. [amount_total between 150 and 500]';
        }

        return null;
    }

    public function reset(Connection $connection): void
    {
        $connection->executeQuery(
            <<<'SQL'
DROP TABLE `order_line_item`;
DROP TABLE `order_address`;
DROP TABLE `order`;
DROP TABLE `order_customer`;
DROP TABLE `tenant`;
SQL
        );
    }
}
