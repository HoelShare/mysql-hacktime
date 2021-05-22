<?php
declare(strict_types=1);

namespace App\Constants;

class Level6
{
    public const TABLE_NAME_TO_COMPARE = 'hogwarts_teacher';

    public const EXPECTED_VIEW_NAME = 'hogwarts_teacher';

    public const TYPE_STUDENT = 'student';

    public const TYPE_TEACHER = 'teacher';

    public const PERSONS = [
        ['name' => 'Harry Potter', 'type' => self::TYPE_STUDENT],
        ['name' => 'Ron Weasley', 'type' => self::TYPE_STUDENT],
        ['name' => 'Hermine Granger', 'type' => self::TYPE_STUDENT],
        ['name' => 'Ginny Weasley', 'type' => self::TYPE_STUDENT],
        ['name' => 'Rubeus Hagrid', 'type' => self::TYPE_TEACHER],
        ['name' => 'Remus Lupin', 'type' => self::TYPE_TEACHER],
        ['name' => 'Severus Snape', 'type' => self::TYPE_TEACHER],
        ['name' => 'Horace Slughorn', 'type' => self::TYPE_TEACHER],
        ['name' => 'Dolores Umbridge', 'type' => self::TYPE_TEACHER],
        ['name' => 'Albus Dumbledore', 'type' => self::TYPE_TEACHER],
        ['name' => 'Minerva McGonagall', 'type' => self::TYPE_TEACHER],
        ['name' => 'Fred Wesley', 'type' => self::TYPE_STUDENT],
        ['name' => 'Luna Lovegood', 'type' => self::TYPE_STUDENT],
        ['name' => 'Neville Longbottom', 'type' => self::TYPE_STUDENT],
    ];
}
