<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Exception\LevelNotFoundException;
use App\Service\LevelService;
use App\Service\UserService;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as SymfonyKernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class KernelTestCase extends SymfonyKernelTestCase
{
    public const TEST_USER = 'test';
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


    public function testCheckALevelFails(): void
    {
        try {
            $error = $this->levelService->checkMax(self::TEST_USER);
            static::assertNotNull($error);
        } catch (\Exception $exception) {
            static::assertInstanceOf(LevelNotFoundException::class, $exception);
        }
    }

    public function testCheckALevelFailsMultipleTimes(): void
    {
        try {
            static::assertNotNull($this->levelService->checkMax(self::TEST_USER));
            static::assertNotNull($this->levelService->checkMax(self::TEST_USER));
            static::assertNotNull($this->levelService->checkMax(self::TEST_USER));
            static::assertNotNull($this->levelService->checkMax(self::TEST_USER));
        } catch (\Exception $exception) {
            static::assertInstanceOf(LevelNotFoundException::class, $exception);
        }
    }
}