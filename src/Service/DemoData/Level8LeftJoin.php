<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Level8;
use Doctrine\DBAL\Connection;

class Level8LeftJoin extends ViewCompareLevel
{
    public function create(Connection $connection): void
    {
    }

    public function cleanUp(Connection $connection): void
    {
    }

    public function getLevel(): int
    {
        return 8;
    }

    public function getDescription(): string
    {
        return sprintf(
            'Due to a mistake, the list of the previous level should also include pilots which does not fly any star ship.
            Viewname: %s
            Columns: pilot_id, pilot_name, ship_id, ship_name, ship_manufacturer',
            Level8::EXPECTED_VIEW_NAME,
        );
    }

    public function validate(Connection $connection, string $username): ?string
    {
        return $this->validateView(
            $connection,
            Level8::EXPECTED_VIEW_NAME,
            Level8::VIEW_NAME_TO_COMPARE
        );
    }

    public function reset(Connection $connection): void
    {
    }
}
