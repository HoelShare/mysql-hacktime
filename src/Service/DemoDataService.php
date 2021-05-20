<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\LevelNotFoundException;
use App\Service\DemoData\DemoDataInterface;
use Doctrine\DBAL\Connection;

class DemoDataService
{
    /**
     * @param iterable|DemoDataInterface[] $demoDataServices
     */
    public function __construct(
        private ConnectionFactory $connectionFactory,
        private iterable $demoDataServices
    ) {
    }

    public function createDemoData(string $username, int $level): void
    {
        $demoDataService = $this->getLevel($level);
        $connection = $this->connectionFactory->createForUser($username);

        $demoDataService->create($connection);
        $this->insertLevelDescription($connection, $level, $demoDataService->getDescription());
    }

    public function checkLevel(string $username, int $level): ?string
    {
        $demoDataService = $this->getLevel($level);
        $connection = $this->connectionFactory->createForUser($username);

        $success = $demoDataService->validate($connection);

        if ($success) {
            $demoDataService->cleanUp($connection);
            try {
                $this->createDemoData($username, $level + 1);
            } catch (LevelNotFoundException $exception) {
                // ignore -> finished
            }
        }

        return $success;
    }

    private function getLevel(int $level): DemoDataInterface
    {
        foreach ($this->demoDataServices as $demoDataService) {
            if ($level === $demoDataService->getLevel()) {
                return $demoDataService;
            }
        }

        throw new LevelNotFoundException($level);
    }

    private function insertLevelDescription(Connection $connection, int $level, string $description): void
    {
        $connection->executeQuery(
            <<<'SQL'
            INSERT INTO level (number, description) VALUES (:level, :description)
SQL
            ,
            ['level' => $level, 'description' => $description]
        );
    }

    public function resetLevel(string $user, int $level): void
    {
        $connection = $this->connectionFactory->createForUser($user);
        $demoData = $this->getLevel($level);
        $demoData->reset($connection);
        if ($level > 0) {
            $connection->executeQuery('DELETE FROM level where number = :level', ['level' => $level]);
        }
        $this->createDemoData($user, $level);
    }
}