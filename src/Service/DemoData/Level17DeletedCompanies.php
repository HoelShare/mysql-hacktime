<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Level16;
use App\Constants\Level17;
use Doctrine\DBAL\Connection;

class Level17DeletedCompanies extends ViewCompareLevel
{
    public function create(Connection $connection): void
    {
    }

    public function cleanUp(Connection $connection): void
    {
    }

    public function getLevel(): int
    {
        return 17;
    }

    public function getDescription(): string
    {
        return sprintf(
            'We realized that we also need the companies which are deleted. (Latest Entry has current flag = 0). So we should add them to the list (%s) of the previous level.
            Add a `is_deleted`',
            Level16::EXPECTED_VIEW_NAME
        );
    }

    protected function getMainViewName(): string
    {
        return Level17::VIEW_NAME_TO_COMPARE;
    }

    public function validate(Connection $connection, string $username): ?string
    {
        return $this->validateView($connection, Level16::EXPECTED_VIEW_NAME);
    }

    public function reset(Connection $connection): void
    {
    }
}
