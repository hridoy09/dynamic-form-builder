<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $this->configureTestDatabase();

        return parent::createApplication();
    }

    protected function configureTestDatabase(): void
    {
        if ($this->usesSqliteInMemory() && ! extension_loaded('pdo_sqlite')) {
            if (! extension_loaded('pdo_mysql')) {
                throw new RuntimeException('PHPUnit is configured for SQLite, but neither pdo_sqlite nor pdo_mysql is available.');
            }

            $this->setEnvironmentValue('DB_CONNECTION', 'mysql');
            $this->clearEnvironmentValue('DB_DATABASE');
            $this->clearEnvironmentValue('DB_URL');
        }
    }

    protected function usesSqliteInMemory(): bool
    {
        return $this->environmentValue('DB_CONNECTION') === 'sqlite'
            && $this->environmentValue('DB_DATABASE') === ':memory:';
    }

    protected function environmentValue(string $key): ?string
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: null;
    }

    protected function setEnvironmentValue(string $key, string $value): void
    {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    protected function clearEnvironmentValue(string $key): void
    {
        putenv($key);
        unset($_ENV[$key], $_SERVER[$key]);
    }
}
