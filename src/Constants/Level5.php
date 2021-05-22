<?php declare(strict_types=1);

namespace App\Constants;

class Level5
{
    public const TABLE_NAME_TO_COMPARE = 'filtered_user';
    public const EXPECTED_VIEW_NAME = 'user';

    public const NAMES = [
        ['name' => 'Hannah Miller', 'test' => 0],
        ['name' => 'Harry Black', 'test' => 1],
        ['name' => 'Emily Brown', 'test' => 0],
        ['name' => 'Sophie Torres', 'test' => 0],
        ['name' => 'Oscar Voldemort', 'test' => 1],
        ['name' => 'Matthew Carolinus', 'test' => 0],
        ['name' => 'Lucy Green', 'test' => 0],
        ['name' => 'Amy Price', 'test' => 1],
        ['name' => 'Thomas Yaztromo', 'test' => 0],
    ];
}