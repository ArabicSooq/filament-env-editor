<?php

namespace ArabicSooq\FilamentEnvEditor\Tests;

use ArabicSooq\FilamentEnvEditor\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpTempDotEnvFile();
    }

    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
            \GeoSot\EnvEditor\ServiceProvider::class,
        ];
    }

    /**
     * Write a sample .env into the location the EnvEditor will actually read from
     * (app()->environmentFilePath()), and point the backup directory to a temp dir.
     */
    protected function setUpTempDotEnvFile(): void
    {
        $envPath = $this->app->environmentFilePath();
        $envDir = dirname($envPath);

        if (!is_dir($envDir)) {
            mkdir($envDir, 0777, true);
        }

        file_put_contents($envPath, $this->sampleEnvContent());

        $backupDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'filament-env-editor-backups-'.uniqid();
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0777, true);
        }

        config()->set('env-editor.paths.backupDirectory', $backupDir);
    }

    protected function sampleEnvContent(): string
    {
        return <<<'ENV'
APP_NAME="Filament Env Editor"
APP_ENV=local
APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=
APP_DEBUG=true

# Database configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306

# Mail configuration
MAIL_MAILER=smtp
MAIL_HOST=mailhog
ENV;
    }
}
