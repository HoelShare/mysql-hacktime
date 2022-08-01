<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Exception\LevelNotFoundException;
use App\Service\LevelService;
use App\Service\UserService;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as SymfonyKernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class KernelTestCase extends SymfonyKernelTestCase
{
    public const TEST_USER = UserService::TEST_USER;
    protected Connection $connection;
    protected ContainerInterface $containerInterface;
    protected LevelService $levelService;

    /**
     * @before
     */
    protected function init(): void
    {
        self::bootKernel();
        $this->containerInterface = self::$kernel->getContainer()->get('test.service_container');
        $this->connection = $this->containerInterface->get(Connection::class);
        $this->levelService = $this->containerInterface->get(LevelService::class);
    }

    protected function assertView(): void
    {
        $error = $this->levelService->checkMax(self::TEST_USER);
        static::assertSame(
            'The result seems to be wrong, possible mistakes. The view contains invalid data or wrong column names.',
            $error
        );
    }
}