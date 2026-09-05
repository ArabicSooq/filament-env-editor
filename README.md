# Filament Env Editor

[![Latest Version on Packagist](https://img.shields.io/packagist/v/arabicsooq/filament-env-editor.svg?style=flat-square)](https://packagist.org/packages/arabicsooq/filament-env-editor)
[![Total Downloads](https://img.shields.io/packagist/dt/arabicsooq/filament-env-editor.svg?style=flat-square)](https://packagist.org/packages/arabicsooq/filament-env-editor)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat-square)](https://github.com/ArabicSooq/filament-env-editor)
[![License](https://img.shields.io/github/license/ArabicSooq/filament-env-editor?style=flat-square)](LICENSE.md)

<div dir="rtl" align="center">
    <img src="https://github.com/GeoSot/filament-env-editor/assets/22406063/e53b56d9-3e2d-4943-b1bd-4b18b6d5fc15" alt="Banner" style="width: 100%; max-width: 800px; border-radius: 10px" />
</div>

A simple, yet powerful **.env file editor** plugin for your Filament Panels.

Manage your current `.env` variables straight from the admin panel, with a handy **backup** functionality page to keep your environment safe.

<br>

## About this package

This package is a **maintained and re-developed fork** of the excellent
[GeoSot/filament-env-editor](https://github.com/GeoSot/filament-env-editor).

We merged the best of both worlds:

- The latest **Filament 5** codebase (Schema API, improved refresh flow, better UI) from the upstream repository.
- The **Arabic documentation** and **Laravel 12/13 support** originally contributed in the `ArabicSooq` fork.
- Fixed multiple bugs that existed in the older fork (broken page refresh, duplicate component keys, editable key names, missing success/error feedback).

The package remains **fully independent** and does not depend on any application-specific code.

> **Credits:** All credit for the original package goes to [Geo Sot](https://github.com/GeoSot). This fork only adds maintenance, fixes, and additional language support.

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
