<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\DBAL\Platforms\SqlitePlatform;

abstract class ApiTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();

        $this->resetDatabase();
    }

    protected function client(): KernelBrowser
    {
        return $this->client;
    }

    protected function getResponseData(): mixed
    {
        $content = $this->client()->getResponse()->getContent();

        if ($content === false) {
            return null;
        }

        return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Очищает тестовую БД и прогоняет миграции.
     */
    private function resetDatabase(): void
    {
        if (!static::getContainer()->has('doctrine')) {
            return;
        }

        $em         = static::getContainer()->get('doctrine')->getManager();
        $connection = $em->getConnection();
        $platform   = $connection->getDatabasePlatform();

        if ($platform instanceof SqlitePlatform) {
            $dbPath = static::getContainer()->getParameter('kernel.project_dir').'/var/test.db';
            if (file_exists($dbPath)) {
                @unlink($dbPath);
            }
        }

        if (static::getContainer()->has('doctrine.migrations.migrator')) {
            $migrator = static::getContainer()->get('doctrine.migrations.migrator');
            $migrator->migrate();
        }
    }

    /**
     * @throws \JsonException
     */
    protected function assertJsonContains(array $expected, string $message = ''): void
    {
        $data = $this->getResponseData();

        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $data, $message ?: "Response should contain key '$key'");
            $this->assertSame($value, $data[$key], $message ?: "Value for key '$key' should match");
        }
    }
}
