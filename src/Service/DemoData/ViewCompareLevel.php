<?php

declare(strict_types=1);

namespace App\Service\DemoData;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\InvalidFieldNameException;
use Doctrine\DBAL\Exception\TableNotFoundException;

abstract class ViewCompareLevel implements DemoDataInterface
{
    public function __construct(
        protected Connection $rootConnection,
    ) {
    }

    protected function validateView(
        Connection $connection,
        string $viewName,
    ): ?string {
        $tableNameToCompare = $this->getMainViewName();

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
        $definition = $connection->fetchOne(
            'SELECT view_definition FROM information_schema.views where VIEW_DEFINITION <> :empty and TABLE_SCHEMA = :schema and TABLE_NAME = :name',
            ['schema' => $connection->getDatabase(), 'name' => $viewName, 'empty' => '']
        );

        if ($definition === false) {
            return null;
        }

        return $definition;
    }

    protected function checkResultSame(Connection $connection, string $viewName): ?string
    {
        $mainView = $this->getMainViewName();

        try {
            $condition = $this->rootConnection->fetchOne(
                <<<'SQL'
SELECT 
    GROUP_CONCAT(' (src.',
        column_name,
        ' <=> ',
        ' dst.',
        column_name, ') ' ORDER BY ORDINAL_POSITION
        SEPARATOR ' AND ')
FROM
    information_schema.columns
WHERE
    table_name = :tableName
AND TABLE_SCHEMA = :mainSchema
SQL
                ,
                ['tableName' => $mainView, 'mainSchema' => $this->rootConnection->getDatabase()]
            );

            $countColumn = $this->rootConnection->fetchOne(
                <<<'SQL'
SELECT 
    column_name
FROM
    information_schema.columns
WHERE
    table_schema = :mainSchema
        AND table_name = :tableName
        AND (is_nullable = 'NO'
        OR ordinal_position = 1)
ORDER BY ordinal_position, is_nullable
SQL
                ,
                ['tableName' => $mainView, 'mainSchema' => $this->rootConnection->getDatabase()]
            );

            $countSame = (int) $this->rootConnection->fetchOne(
                sprintf(
                    'SELECT count(dst.%s) FROM %s src left join %s.%s dst ON %s',
                    $countColumn,
                    $mainView,
                    $connection->getDatabase(),
                    $viewName,
                    $condition,
                )
            );
            $countExpected = (int) $this->rootConnection->fetchOne(sprintf('SELECT count(0) FROM %s', $mainView));

            if ($countSame === $countExpected) {
                return null;
            }

            return 'The result seems to be wrong, did you filter the expected?';
        } catch (TableNotFoundException $exception) {
            return sprintf('Did you name the View %s', $viewName);
        } catch (InvalidFieldNameException $exception) {
            $fieldNames = $this->rootConnection->fetchOne(
                <<<'SQL'
SELECT 
    GROUP_CONCAT(column_name ORDER BY ORDINAL_POSITION)
FROM
    information_schema.columns
WHERE
    table_name = :tableName
AND TABLE_SCHEMA = :mainSchema
SQL
                ,
                ['tableName' => $mainView, 'mainSchema' => $this->rootConnection->getDatabase()]
            );

            return sprintf(
                'Did you name the columns as expected?
            %s',
                $fieldNames
            );
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function getPreviewData(): ?array
    {
        try {
            return $this->rootConnection->fetchAllAssociative(sprintf('SELECT * FROM %s LIMIT 10', $this->getMainViewName()));
        } catch (Exception $exception) {
            return null;
        }
    }

    abstract protected function getMainViewName(): string;
}
