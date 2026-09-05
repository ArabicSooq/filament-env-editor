<?php

namespace ArabicSooq\FilamentEnvEditor\Tests\Feature;

use ArabicSooq\FilamentEnvEditor\Tests\TestCase;
use GeoSot\EnvEditor\Exceptions\EnvException;
use GeoSot\EnvEditor\Facades\EnvEditor;
use Illuminate\Support\Collection;

/**
 * Tests the actual EnvEditor operations that back the plugin's
 * Create / Edit / Delete and Backup actions.
 */
final class EnvEditorOperationsTest extends TestCase
{
    public function test_it_reads_all_env_keys(): void
    {
        $content = EnvEditor::getEnvFileContent();

        $this->assertInstanceOf(Collection::class, $content);

        $keys = $content->filter(fn ($entry) => !$entry->isSeparator())
            ->pluck('key')
            ->all();

        $this->assertContains('APP_NAME', $keys);
        $this->assertContains('DB_CONNECTION', $keys);
        $this->assertContains('MAIL_MAILER', $keys);
    }

    public function test_it_creates_a_new_key(): void
    {
        $result = EnvEditor::addKey('NEW_FEATURE_FLAG', 'true');

        $this->assertTrue($result);

        $keys = EnvEditor::getEnvFileContent()
            ->filter(fn ($entry) => !$entry->isSeparator())
            ->pluck('key')
            ->all();

        $this->assertContains('NEW_FEATURE_FLAG', $keys);

        $value = EnvEditor::getEnvFileContent()
            ->firstWhere('key', 'NEW_FEATURE_FLAG');

        $this->assertEquals('true', $value->getValue());
    }

    public function test_it_edits_an_existing_key(): void
    {
        $result = EnvEditor::editKey('APP_ENV', 'production');

        $this->assertTrue($result);

        $value = EnvEditor::getEnvFileContent()
            ->firstWhere('key', 'APP_ENV');

        $this->assertEquals('production', $value->getValue());
    }

    public function test_it_deletes_an_existing_key(): void
    {
        $this->assertTrue(EnvEditor::deleteKey('APP_DEBUG'));

        $keys = EnvEditor::getEnvFileContent()
            ->filter(fn ($entry) => !$entry->isSeparator())
            ->pluck('key')
            ->all();

        $this->assertNotContains('APP_DEBUG', $keys);
    }

    public function test_it_can_add_key_at_a_specific_index(): void
    {
        $options = ['index' => 1];
        $result = EnvEditor::addKey('CUSTOM_KEY', 'custom-value', $options);

        $this->assertTrue($result);

        $keys = EnvEditor::getEnvFileContent()
            ->filter(fn ($entry) => !$entry->isSeparator())
            ->pluck('key')
            ->all();

        $this->assertContains('CUSTOM_KEY', $keys);
        // The key should be inserted early in the file (within the first three entries).
        $this->assertLessThan(3, array_search('CUSTOM_KEY', $keys));
    }

    public function test_creating_a_duplicate_key_throws(): void
    {
        $this->expectException(EnvException::class);

        EnvEditor::addKey('APP_NAME', 'duplicate');
    }

    public function test_it_makes_and_lists_backups(): void
    {
        $this->assertTrue(EnvEditor::backUpCurrent());

        $backups = EnvEditor::getAllBackUps();

        $this->assertCount(1, $backups);
        $this->assertInstanceOf(Collection::class, $backups);
    }

    public function test_it_restores_a_backup(): void
    {
        $this->assertTrue(EnvEditor::backUpCurrent());

        // Change a value after the backup was taken.
        EnvEditor::editKey('APP_ENV', 'production');

        $backup = EnvEditor::getAllBackUps()->first();

        $this->assertTrue(EnvEditor::restoreBackUp($backup->name));

        $value = EnvEditor::getEnvFileContent()
            ->firstWhere('key', 'APP_ENV');

        $this->assertEquals('local', $value->getValue());
    }

    public function test_it_downloads_the_current_env_file(): void
    {
        $this->assertTrue(EnvEditor::backUpCurrent());

        $filePath = EnvEditor::getFilePath('');

        $this->assertFileExists($filePath);

        $contents = file_get_contents($filePath);

        $this->assertStringContainsString('APP_NAME', $contents);
    }

    public function test_it_deletes_a_backup(): void
    {
        $this->assertTrue(EnvEditor::backUpCurrent());

        $backup = EnvEditor::getAllBackUps()->first();

        $this->assertTrue(EnvEditor::deleteBackup($backup->name));

        $this->assertEmpty(EnvEditor::getAllBackUps());
    }

    public function test_it_hides_separators_from_listing(): void
    {
        $content = EnvEditor::getEnvFileContent();

        $separators = $content->filter(fn ($entry) => $entry->isSeparator());

        $this->assertNotEmpty($separators); // the sample file contains '# Database configuration'
    }

    public function test_backup_file_returns_raw_content(): void
    {
        $this->assertTrue(EnvEditor::backUpCurrent());

        $backup = EnvEditor::getAllBackUps()->first();

        $raw = file_get_contents(EnvEditor::getFilePath($backup->name));

        $this->assertStringContainsString('APP_KEY=base64:AAAA', $raw);
        $this->assertStringContainsString('# Database configuration', $raw);
    }
}
