<?php

namespace ArabicSooq\FilamentEnvEditor\Tests\Feature;

use ArabicSooq\FilamentEnvEditor\Support\EnvValidation;
use ArabicSooq\FilamentEnvEditor\Tests\TestCase;

final class EnvValidationTest extends TestCase
{
    public function test_valid_key_name_passes(): void
    {
        $this->assertNull(EnvValidation::validateKeyName('APP_NAME'));
        $this->assertNull(EnvValidation::validateKeyName('DB_PASSWORD_2'));
    }

    public function test_invalid_key_name_is_rejected(): void
    {
        $this->assertNotNull(EnvValidation::validateKeyName(''));
        $this->assertNotNull(EnvValidation::validateKeyName('APP NAME'));
        $this->assertNotNull(EnvValidation::validateKeyName('APP-NAME'));
        $this->assertNotNull(EnvValidation::validateKeyName(str_repeat('A', 101)));
    }

    public function test_dangerous_values_are_rejected(): void
    {
        $this->assertNotNull(EnvValidation::validateValue('FOO', '$(rm -rf /)'));
        $this->assertNotNull(EnvValidation::validateValue('FOO', '`whoami`'));
        $this->assertNotNull(EnvValidation::validateValue('FOO', "foo;\nbar"));
    }

    public function test_normal_values_pass_validation(): void
    {
        $this->assertNull(EnvValidation::validateValue('APP_NAME', 'My App'));
        $this->assertNull(EnvValidation::validateValue('DB_PORT', '3306'));
        $this->assertNull(EnvValidation::validateValue('APP_DEBUG', 'true'));
    }

    public function test_critical_keys_cannot_be_emptied(): void
    {
        $this->assertFalse(EnvValidation::canEmptyKey('APP_KEY'));
        $this->assertFalse(EnvValidation::canEmptyKey('APP_ENV'));
        $this->assertTrue(EnvValidation::canEmptyKey('APP_NAME'));
    }

    public function test_critical_keys_cannot_be_deleted(): void
    {
        $this->assertFalse(EnvValidation::canDeleteKey('APP_KEY'));
        $this->assertFalse(EnvValidation::canDeleteKey('APP_ENV'));
        $this->assertTrue(EnvValidation::canDeleteKey('APP_NAME'));
    }

    public function test_protected_keys_and_wildcards_are_respected(): void
    {
        $this->assertFalse(EnvValidation::canDeleteKey('AWS_SECRET', ['AWS_*']));
        $this->assertFalse(EnvValidation::canDeleteKey('MAIL_PASSWORD', ['MAIL_PASSWORD']));
        $this->assertTrue(EnvValidation::canDeleteKey('AWS_BUCKET', []));
    }

    public function test_weakens_key_detects_empty_proposal(): void
    {
        $this->assertTrue(EnvValidation::weakensKey('APP_KEY', '', 'base64:something'));
        $this->assertFalse(EnvValidation::weakensKey('APP_NAME', '', ''));
    }
}
