<?php

namespace ArabicSooq\FilamentEnvEditor\Tests\Feature;

use ArabicSooq\FilamentEnvEditor\FilamentEnvEditorPlugin;
use ArabicSooq\FilamentEnvEditor\Pages\ViewEnv;
use ArabicSooq\FilamentEnvEditor\Tests\TestCase;

final class PluginTest extends TestCase
{
    public function test_plugin_can_be_instantiated(): void
    {
        $plugin = FilamentEnvEditorPlugin::make();

        $this->assertInstanceOf(FilamentEnvEditorPlugin::class, $plugin);
    }

    public function test_plugin_get_id(): void
    {
        $this->assertSame('filament-env-editor', FilamentEnvEditorPlugin::make()->getId());
    }

    public function test_plugin_has_default_view_page(): void
    {
        $plugin = FilamentEnvEditorPlugin::make();

        $this->assertSame(ViewEnv::class, $plugin->getViewPage());
    }

    public function test_plugin_defaults(): void
    {
        $plugin = FilamentEnvEditorPlugin::make();

        $this->assertTrue($plugin->isAuthorized());
        $this->assertSame('env-editor', $plugin->getSlug());
        $this->assertSame('heroicon-o-document-text', $plugin->getNavigationIcon());
        $this->assertSame(1, $plugin->getNavigationSort());
        $this->assertIsArray($plugin->getHiddenKeys());
        $this->assertEmpty($plugin->getHiddenKeys());
    }

    public function test_plugin_fluent_configuration(): void
    {
        $plugin = FilamentEnvEditorPlugin::make()
            ->slug('my-env-editor')
            ->navigationGroup('Settings')
            ->navigationLabel('Environment Variables')
            ->navigationSort(10)
            ->navigationIcon('heroicon-o-cog')
            ->hideKeys('APP_KEY', 'DB_PASSWORD')
            ->authorize(fn (): bool => false);

        $this->assertSame('my-env-editor', $plugin->getSlug());
        $this->assertSame('Settings', $plugin->getNavigationGroup());
        $this->assertSame('Environment Variables', $plugin->getNavigationLabel());
        $this->assertSame(10, $plugin->getNavigationSort());
        $this->assertSame('heroicon-o-cog', $plugin->getNavigationIcon());
        $this->assertSame(['APP_KEY', 'DB_PASSWORD'], $plugin->getHiddenKeys());
        $this->assertFalse($plugin->isAuthorized());
    }

    public function test_plugin_hide_keys_are_respected_in_page(): void
    {
        $plugin = FilamentEnvEditorPlugin::make()->hideKeys('APP_KEY');

        $this->assertSame(['APP_KEY'], $plugin->getHiddenKeys());
    }

    public function test_view_env_exposes_plugin_configuration(): void
    {
        $plugin = FilamentEnvEditorPlugin::make()->authorize(true)->hideKeys('APP_KEY');

        $this->assertTrue($plugin->isAuthorized());
        $this->assertSame(['APP_KEY'], $plugin->getHiddenKeys());
        $this->assertSame(ViewEnv::class, $plugin->getViewPage());
    }
}
