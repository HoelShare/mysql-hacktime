<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Level13;
use Doctrine\DBAL\Connection;

class Level13OrderBy extends ViewCompareLevel
{
    public function create(Connection $connection): void
    {
    }

    public function cleanUp(Connection $connection): void
    {
    }

    public function getLevel(): int
    {
        return 13;
    }

    public function getDescription(): string
    {
        return sprintf(
            'To analyze the orders, it\'s helpful to sort them by the owner [tenant] & also the order date, where the latest orders are on top.
        Viewname: %s',
            Level13::EXPECTED_VIEW_NAME
        );
    }

    public function validate(Connection $connection, string $username): ?string
    {
        return $this->validateView($connection, Level13::EXPECTED_VIEW_NAME, Level13::EXPECTED_VIEW_NAME);
    }

    public function reset(Connection $connection): void
    {
    }
}
