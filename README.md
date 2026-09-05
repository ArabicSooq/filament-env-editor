# Filament Env Editor

[![Latest Version on Packagist](https://img.shields.io/packagist/v/arabicsooq/filament-env-editor.svg?style=flat-square)](https://packagist.org/packages/arabicsooq/filament-env-editor)
[![Total Downloads](https://img.shields.io/packagist/dt/arabicsooq/filament-env-editor.svg?style=flat-square)](https://packagist.org/packages/arabicsooq/filament-env-editor)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat-square)](https://github.com/ArabicSooq/filament-env-editor)
[![License](https://img.shields.io/github/license/ArabicSooq/filament-env-editor?style=flat-square)](LICENSE.md)

A simple, yet powerful **.env file editor** plugin for your Filament Panels.

Manage your current `.env` variables straight from the admin panel, with a handy **backup** functionality page to keep your environment safe.

<br>

## What this package does

- **View and manage `.env` variables** directly from your Filament panel, without ever touching the server.
- **Create, edit, and delete** keys and change their values through a graphical interface.
- **Full backup of the current `.env` file** and saved, restorable backups.
- **Restore any backup** in one click to roll your environment back to a previous state.
- **Download and upload backups** to keep or import copies locally.
- **Hide sensitive keys** (such as `APP_KEY`) so they never appear in the interface.
- **Flexible customization** of navigation, the page, and access control through plugin configuration.
- Compatible with **Filament 5** and **Laravel 12/13**.

<br>

## Requirements

| Component | Version |
|-----------|---------|
| PHP | `^8.2` |
| Laravel | `^12.0 \| ^13.0` |
| Filament | `~5.0` |

## Installation

You can install the package via composer:

```bash
composer require arabicsooq/filament-env-editor
```

## Usage

Add the `ArabicSooq\FilamentEnvEditor\FilamentEnvEditorPlugin` to your panel config.

```php
use ArabicSooq\FilamentEnvEditor\FilamentEnvEditorPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->plugin(
                FilamentEnvEditorPlugin::make()
            );
    }
}
```

## Configuration

### Customizing the navigation item

```php
FilamentEnvEditorPlugin::make()
    ->navigationGroup('System Tools')
    ->navigationLabel('My Env')
    ->navigationIcon('heroicon-o-cog-8-tooth')
    ->navigationSort(1)
    ->slug('env-editor')
```

### Hiding keys

Some keys may be considered particularly sensitive and you may not wish to expose them, even through this package. You can hide them through this interface:

```php
FilamentEnvEditorPlugin::make()
    ->hideKeys('APP_KEY', 'BCRYPT_ROUNDS')
```

### Authorization

If you would like to prevent certain users from accessing the env-editor page, add an `authorize` callback to the plugin chain:

```php
FilamentEnvEditorPlugin::make()
    ->authorize(
        fn () => auth()->user()->isAdmin()
    )
```

### Customizing the env-editor page

To customize the page, extend the `ArabicSooq\FilamentEnvEditor\Pages\ViewEnv` page and override its methods:

```php
use ArabicSooq\FilamentEnvEditor\Pages\ViewEnv as BaseViewEnvEditor;

class ViewEnv extends BaseViewEnvEditor
{
    // Your implementation
}
```

```php
use App\Filament\Pages\ViewEnv;

FilamentEnvEditorPlugin::make()
    ->viewPage(ViewEnv::class)
```

## Testing

```bash
composer install
composer test        # Runs PHPUnit
composer phpstan     # Static analysis (PHPStan)
composer cs          # Code style (PHP-CS-Fixer)
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
