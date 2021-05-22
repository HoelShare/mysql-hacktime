<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Globals;
use App\Constants\Level13;
use App\Constants\Level14;
use App\Constants\Level15;
use Doctrine\DBAL\Connection;

class Level15SubSelectHaving extends ViewCompareLevel
{
    public function create(Connection $connection): void
    {
    }

    public function cleanUp(Connection $connection): void
    {
    }

    public function getLevel(): int
    {
        return 15;
    }

    public function getDescription(): string
    {
        return sprintf(
            'In most cases an order contains 2 or less items. But more important are orders which contain more(!) items.
        Viewname: %s
        Column Names: all columns from the %s table.
        Sort by the tenant, order.id',
            Level15::EXPECTED_VIEW_NAME,
            Globals::TABLE_NAME_ORDERS,
        );
    }

    public function validate(Connection $connection, string $username): ?string
    {
        $validation = $this->validateView(
            $connection,
            Level15::EXPECTED_VIEW_NAME,
            Level15::EXPECTED_VIEW_NAME,
        );
        if ($validation !== null) {
            $validation .= '
            Hint: Try using a SubSelect with a HAVING clause';
        }

        return $validation;
    }

    public function reset(Connection $connection): void
    {
    }
}