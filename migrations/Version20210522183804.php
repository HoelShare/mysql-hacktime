<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use DateInterval;
use DateTime;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Faker\Factory;

final class Version20210522183804 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->connection->executeQuery(
            <<<'SQL'
            CREATE TABLE company (
                id int not null,
                name varchar(250) not null,
                customer_number int not null,
                creation_date datetime not null,
                website varchar(250) null,
                banned tinyint(1) not null default '0',
                test tinyint(1) not null default '0',
                notice varchar(250) null,
                verified tinyint(1) not null default '0',
                net_promoter_score int null,
                VALID_FROM_DTTM datetime not null,
                VALID_TO_DTTM datetime not null,
                CURRENT_FLAG tinyint(1),
                primary key (id, VALID_FROM_DTTM),
                unique key (id, VALID_TO_DTTM),
                key (id, CURRENT_FLAG)
            );

            CREATE TABLE company_business_relation(
                id int not null,
                name varchar(250) not null,
                description tinytext null,
                prio_sort int not null,
                VALID_FROM_DTTM datetime not null ,
                VALID_TO_DTTM datetime not null,
                CURRENT_FLAG tinyint(1) not null,
                primary key (id, VALID_FROM_DTTM),
                unique key (id, VALID_TO_DTTM),
                key (id, CURRENT_FLAG)
            );

            CREATE TABLE company_has_business_relation(
                company_id int not null,
                business_relation_id int not null,
                VALID_FROM_DTTM datetime not null,
                VALID_TO_DTTM datetime NOT null,
                CURRENT_FLAG tinyint(1) NOT NULL,
                primary key (company_id, business_relation_id, VALID_FROM_DTTM),
                unique key (company_id, business_relation_id, VALID_TO_DTTM),
                key (company_id, business_relation_id, CURRENT_FLAG)
             );
SQL
        );

        $faker = Factory::create();
        $companyNumbers = $faker->numberBetween(20, 60);
        $businessRelationEntries = $faker->numberBetween(5, 15);

        for ($companyId = 1; $companyId <= $companyNumbers; $companyId++) {
            $historyEntries = $faker->numberBetween(1, 6);

            $creationDate = $validFrom = $faker->dateTimeBetween('-20 years');
            $verified = false;
            do {
                $verified = $verified ?: $faker->boolean();
                $validTo = $faker->dateTimeBetween($validFrom);
                $currentFlag = 0;

                if ($historyEntries === 1 && $faker->boolean(90)) {
                    $currentFlag = 1;
                    $validTo = new DateTime('9999-12-31 23:59:59');
                }

                $this->connection->executeQuery(
                    'INSERT INTO company (
id,
name,
customer_number,
creation_date,
website,
banned,
test,
notice,
verified,
net_promoter_score,
VALID_FROM_DTTM,
VALID_TO_DTTM,
CURRENT_FLAG
) VALUES (
:id,
:name,
:customer_number,
:creation_date,
:website,
:banned,
:test,
:notice,
:verified,
:net_promoter_score,
:VALID_FROM_DTTM,
:VALID_TO_DTTM,
:CURRENT_FLAG
)',
                    [
                        'id' => $companyId,
                        'name' => $faker->company(),
                        'customer_number' => $faker->randomNumber(),
                        'creation_date' => $creationDate->format(DATE_ATOM),
                        'website' => $faker->domainName(),
                        'banned' => (int) $faker->boolean(2),
                        'test' => (int) $faker->boolean(5),
                        'notice' => $faker->boolean(20) ? implode(' ', $faker->sentences()) : null,
                        'verified' => (int) $verified,
                        'net_promoter_score' => $faker->boolean(30) ? $faker->randomDigit() : null,
                        'VALID_FROM_DTTM' => $validFrom->format(DATE_ATOM),
                        'VALID_TO_DTTM' => $validTo->format(DATE_ATOM),
                        'CURRENT_FLAG' => $currentFlag,
                    ]
                );

                $validFrom = $validTo->add(new DateInterval('PT1S'));
            } while (--$historyEntries);
        }

        for ($businessRelationId = 0; $businessRelationId <= $businessRelationEntries; $businessRelationId++) {
            $historyEntries = $faker->numberBetween(1, 2);

            $validFrom = $faker->dateTimeBetween('-30 years', '-20 years');
            do {
                $validTo = $faker->dateTimeBetween($validFrom);
                $currentFlag = 0;

                if ($historyEntries === 1) {
                    $currentFlag = 1;
                    $validTo = new DateTime('9999-12-31 23:59:59');
                }

                $this->connection->executeQuery(
                    <<<'SQL'
                    INSERT INTO company_business_relation
(id, name, description, prio_sort, valid_from_dttm, valid_to_dttm, current_flag) VALUES 
(:id, :name, :description, :prioSort, :validFrom, :validTo, :current);
SQL
                    ,
                    [
                        'id' => $businessRelationId,
                        'name' => $faker->word(),
                        'description' => implode(' ', $faker->words()),
                        'prioSort' => $faker->randomDigit(),
                        'validFrom' => $validFrom->format(DATE_ATOM),
                        'validTo' => $validTo->format(DATE_ATOM),
                        'current' => $currentFlag,
                    ]
                );

                $validFrom = $validTo->add(new DateInterval('PT1S'));
            } while (--$historyEntries);
        }

        for ($companyId = 1; $companyId <= $companyNumbers; $companyId++) {
            $historyEntries = $faker->numberBetween(1, 4);
            $validFrom = $faker->dateTimeBetween('-20 years');

            do {
                $validTo = $faker->dateTimeBetween($validFrom);
                $currentFlag = 0;

                if ($historyEntries === 1) {
                    $currentFlag = 1;
                    $validTo = new DateTime('9999-12-31 23:59:59');
                }

                $this->connection->executeQuery(
                    <<<'SQL'
                INSERT INTO company_has_business_relation 
(company_id, business_relation_id, VALID_FROM_DTTM, VALID_TO_DTTM, CURRENT_FLAG) VALUES 
(:companyId, :businessRelationId, :validFrom, :validTo, :current)        
SQL
                    ,
                    [
                        'companyId' => $companyId,
                        'businessRelationId' => $faker->numberBetween(1, $businessRelationEntries),
                        'validFrom' => $validFrom->format(DATE_ATOM),
                        'validTo' => $validTo->format(DATE_ATOM),
                        'current' => $currentFlag,
                    ]
                );

                $validFrom = $validTo->add(new DateInterval('PT1S'));
            } while (--$historyEntries);
        }
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
