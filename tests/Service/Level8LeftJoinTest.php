<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Constants\Level5;
use App\Constants\Level6;
use App\Constants\Level7;
use App\Constants\Level8;
use App\Service\DemoData\Level6ReCreateView;
use Doctrine\DBAL\Exception\DriverException;

class Level8LeftJoinTest extends KernelTestCase
{
    /**
     * @before
     */
    public function cleanUp()
    {
        try {
            $this->connection->executeQuery(
                sprintf(
                    'DROP VIEW IF EXISTS %s.%s;',
                    self::TEST_USER,
                    Level8::EXPECTED_VIEW_NAME
                )
            );
        } catch (DriverException $exception) {
        }
    }

    public function testLevel8WrongFilter(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT c.id as pilot_id, 
                           c.name as pilot_name, 
                           s.id as ship_id, 
                           s.name as ship_name, 
                           s.manufacturer as ship_manufacturer 
                    FROM star_wars_character c 
                        INNER JOIN star_wars_ship_pilot swsp on c.id = swsp.pilot_id 
                        LEFT OUTER JOIN star_wars_star_ship s on swsp.ship_id = s.id;
SQL
                ,
                self::TEST_USER,
                Level8::EXPECTED_VIEW_NAME,
            )
        );

        $this->assertView();
    }

    public function testLevel8WrongColumns(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT c.id as pilot_id, 
                           c.name as name, 
                           s.id as ship_id, 
                           s.name as ship, 
                           s.manufacturer as manufacturer 
                    FROM star_wars_character c 
                        LEFT OUTER JOIN star_wars_ship_pilot swsp on c.id = swsp.pilot_id 
                        LEFT OUTER JOIN star_wars_star_ship s on swsp.ship_id = s.id;
SQL
                ,
                self::TEST_USER,
                Level8::EXPECTED_VIEW_NAME
            )
        );

        $this->assertView();
    }


    public function testLevel8Success(): void
    {
        $this->connection->executeQuery(
            sprintf(
                <<<'SQL'
                CREATE OR REPLACE VIEW %s.%s as 
                    SELECT c.id as pilot_id, 
                           c.name as pilot_name, 
                           s.id as ship_id, 
                           s.name as ship_name, 
                           s.manufacturer as ship_manufacturer 
                    FROM star_wars_character c 
                        LEFT OUTER JOIN star_wars_ship_pilot swsp on c.id = swsp.pilot_id 
                        LEFT OUTER JOIN star_wars_star_ship s on swsp.ship_id = s.id;
SQL
                ,
                self::TEST_USER,
                Level8::EXPECTED_VIEW_NAME,
            )
        );

        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertNull($error);
    }
}