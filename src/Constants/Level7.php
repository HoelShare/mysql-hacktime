<?php
declare(strict_types=1);

namespace App\Constants;

class Level7
{
    public const EXPECTED_VIEW_NAME = 'star_wars_character_ships';

    public const STAR_SHIPS = [
        ['id' => 2, 'name' => 'CR90 corvette', 'passenger' => 600, 'manufacturer' => 'Corellian Engineering Corporation', 'length' => 150],
        ['id' => 3, 'name' => 'Star Destroyer', 'passenger' => null, 'manufacturer' => 'Kuat Drive Yards', 'length' => 1600],
        ['id' => 5, 'name' => 'Sentinel-class landing craft', 'passenger' => 75, 'manufacturer' => 'Sienar Fleet Systems, Cyngus Spaceworks', 'length' => 38],
        ['id' => 9, 'name' => 'Death Star', 'passenger' => 843342, 'manufacturer' => 'Imperial Department of Military Research, Sienar Fleet Systems', 'length' => 120000],
        ['id' => 10, 'name' => 'Millennium Falcon', 'passenger' => 6, 'manufacturer' => 'Corellian Engineering Corporation', 'length' => 34.37],
        ['id' => 11, 'name' => 'Y-wing', 'passenger' => 0, 'manufacturer' => 'Koensayr Manufacturing', 'length' => 14],
        ['id' => 12, 'name' => 'X-wing', 'passenger' => 0, 'manufacturer' => 'Incom Corporation', 'length' => 12.5],
        ['id' => 22, 'name' => 'Imperial shuttle', 'passenger' => 20, 'manufacturer' => 'Sienar Fleet Systems', 'length' => 20],
        ['id' => 13, 'name' => 'TIE Advanced x1', 'passenger' => 0, 'manufacturer' => 'Sienar Fleet Systems', 'length' => 9.2],
    ];

    public const PEOPLE = [
        ['id' => 1, 'name' => 'Luke Skywalker', 'height' => 172, 'gender' => 'male'],
        ['id' => 2, 'name' => 'C-3PO', 'height' => 167, 'gender' => null],
        ['id' => 3, 'name' => 'R2-D2', 'height' => 96, 'gender' => null],
        ['id' => 4, 'name' => 'Darth Vader', 'height' => 202, 'gender' => 'male'],
        ['id' => 5, 'name' => 'Leia Organa', 'height' => 150, 'gender' => 'female'],
        ['id' => 13, 'name' => 'Chewbacca', 'height' => 228, 'gender' => 'male'],
        ['id' => 14, 'name' => 'Han Solo', 'height' => 180, 'gender' => 'male'],
        ['id' => 28, 'name' => 'Mon Mothma', 'height' => 150, 'gender' => 'female'],
    ];

    public const SHIP_PILOT = [
        ['ship_id' => 10, 'pilot_id' => 14],
        ['ship_id' => 22, 'pilot_id' => 14],
        ['ship_id' => 10, 'pilot_id' => 13],
        ['ship_id' => 22, 'pilot_id' => 13],
        ['ship_id' => 12, 'pilot_id' => 1],
        ['ship_id' => 22, 'pilot_id' => 1],
        ['ship_id' => 13, 'pilot_id' => 4],
    ];
}
