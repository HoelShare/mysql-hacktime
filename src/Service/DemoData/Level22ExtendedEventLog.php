<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Globals;
use App\Constants\Level22;
use Doctrine\DBAL\Connection;

class Level22ExtendedEventLog extends ViewCompareLevel
{
    public function create(Connection $connection): void
    {
    }

    public function cleanUp(Connection $connection): void
    {
    }

    public function getLevel(): int
    {
        return 22;
    }

    public function getDescription(): string
    {
        return sprintf(
            'Create an extended event log view (%s), which contains all events (%s) + an additional column which represents the days since the first log entry. (Remember the multi tenant support)
            Extra Column: days_since_first_log',
            Level22::EXPECTED_VIEW_NAME,
            Globals::TABLE_EVENT_LOG,
        );
    }

    public function validate(Connection $connection, string $username): ?string
    {
        return $this->checkResultSame($connection, Level22::EXPECTED_VIEW_NAME, Level22::EXPECTED_VIEW_NAME);
    }

    public function reset(Connection $connection): void
    {
    }
}
