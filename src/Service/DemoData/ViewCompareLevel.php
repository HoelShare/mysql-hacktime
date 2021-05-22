<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use App\Constants\Level5;
use Doctrine\DBAL\Connection;

abstract class ViewCompareLevel implements DemoDataInterface
{
    public function __construct(
        protected Connection $rootConnection,
    ) {
    }

    protected function validateView(
        Connection $connection,
        string $viewName,
        string $tableNameToCompare
    ): ?string {
        $baseType = $connection->fetchOne(
            'SELECT TABLE_TYPE from information_schema.tables where TABLE_SCHEMA = :userSchema and TABLE_NAME = :table',
            [
                'userSchema' => $connection->getDatabase(),
                'table' => $viewName,
            ]
        );

        if ($baseType === false) {
            return 'Did you try to create the view and just used a wrong name?';
        }

        if ($baseType !== 'VIEW') {
            return 'It does not look like a view?';
        }

        $rows = $connection
            ->createQueryBuilder()
            ->select('*')
            ->from($viewName)
            ->execute()
            ->fetchAllAssociative();

        $compareRows = $this->rootConnection
            ->createQueryBuilder()
            ->select('*')
            ->from($tableNameToCompare)
            ->execute()
            ->fetchAllAssociative();

        if ($compareRows !== $rows) {
            return 'The result seems to be wrong, possible mistakes. The view contains invalid data or wrong column names.';
        }

        return null;
    }

    protected function getViewDefinition(
        Connection $connection,
        string $viewName,
    ): ?string {
        $name = $this->rootConnection->fetchOne(
            'SELECT view_definition FROM information_schema.views where VIEW_DEFINITION <> :empty and TABLE_SCHEMA = :schema and TABLE_NAME = :name',
            ['schema' => $connection->getDatabase(), 'name' => $viewName, 'empty' => '']
        );

        if ($name === false) {
            return null;
        }

        return $name;
    }
}