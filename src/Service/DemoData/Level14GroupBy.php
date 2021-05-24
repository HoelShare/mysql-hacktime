<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Level14;
use Doctrine\DBAL\Connection;

class Level14GroupBy extends ViewCompareLevel
{
    public function create(Connection $connection): void
    {
    }

    public function cleanUp(Connection $connection): void
    {
    }

    public function getLevel(): int
    {
        return 14;
    }

    public function getDescription(): string
    {
        return sprintf(
            'We need an overview, how many orders and how high the total amount per tenant is.
        Viewname: %s
        Column Names: tenant_id, total_amount, order_count
        Sort by the total amount descending',
            Level14::EXPECTED_VIEW_NAME
        );
    }

    protected function getMainViewName(): string
    {
        return Level14::EXPECTED_VIEW_NAME;
    }

    public function validate(Connection $connection, string $username): ?string
    {
        return $this->validateView(
            $connection,
            Level14::EXPECTED_VIEW_NAME,
        );
    }

    public function reset(Connection $connection): void
    {
    }
}
