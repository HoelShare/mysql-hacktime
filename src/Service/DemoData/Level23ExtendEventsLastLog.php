<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Level22;
use App\Constants\Level23;
use Doctrine\DBAL\Connection;

class Level23ExtendEventsLastLog extends ViewCompareLevel
{
    public function create(Connection $connection): void
    {
    }

    public function cleanUp(Connection $connection): void
    {
    }

    public function getLevel(): int
    {
        return 23;
    }

    public function getDescription(): string
    {
        return sprintf(
            'Extended the event log view (%s) from the last level with the days since the last action. (Remember the multi tenant support)
            Extra Column: days_since_last_log',
            Level22::EXPECTED_VIEW_NAME,
        );
    }

    public function validate(Connection $connection, string $username): ?string
    {
        $result = $this->checkResultSame($connection, Level22::EXPECTED_VIEW_NAME, Level23::VIEW_NAME_TO_COMPARE);

        if ($result !== null) {
            return $result;
        }

        $viewDefinition = $this->getViewDefinition($connection, Level22::EXPECTED_VIEW_NAME);

        if (mb_stripos($viewDefinition, ' over') === false) {
            return 'For performance reasons a WINDOW Function should be used!';
        }

        return null;
    }

    public function reset(Connection $connection): void
    {
    }
}
