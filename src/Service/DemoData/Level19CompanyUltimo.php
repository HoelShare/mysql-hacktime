<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Globals;
use App\Constants\Level19;
use Doctrine\DBAL\Connection;

class Level19CompanyUltimo extends ViewCompareLevel
{
    public function create(Connection $connection): void
    {
    }

    public function cleanUp(Connection $connection): void
    {
    }

    public function getLevel(): int
    {
        return 19;
    }

    public function getDescription(): string
    {
        return sprintf(
            'We\'d like to analyze the companies in a year timeframe (Name: %s). Please join the company with the ultimo view (%s) of the previous level.
            Timeframe: Month_diff between -12 and 0.
            Columns: ultimo, all company (except VALID_FROM_DTTM, VALID_TO_DTTM, CURRENT_FLAG)',
            Level19::EXPECTED_VIEW_NAME,
            Globals::VIEW_NAME_ULTIMO,
        );
    }

    protected function getMainViewName(): string
    {
        return Level19::VIEW_NAME_TO_COMPARE;
    }

    public function validate(Connection $connection, string $username): ?string
    {
        return $this->checkResultSame($connection, Level19::EXPECTED_VIEW_NAME);
    }

    public function reset(Connection $connection): void
    {
    }
}
