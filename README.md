# Filament Env Editor

[![Latest Version on Packagist](https://img.shields.io/packagist/v/arabicsooq/filament-env-editor.svg?style=flat-square)](https://packagist.org/packages/arabicsooq/filament-env-editor)
[![Total Downloads](https://img.shields.io/packagist/dt/arabicsooq/filament-env-editor.svg?style=flat-square)](https://packagist.org/packages/arabicsooq/filament-env-editor)

<p align="center">
    <img src="https://github.com/ArabicSooq/filament-env-editor/assets/22406063/e53b56d9-3e2d-4943-b1bd-4b18b6d5fc15" alt="Banner" style="width: 100%; max-width: 800px; border-radius: 10px" />
</p>

 A Simple .env file Viewer plugin for your Filament Panels.
 
 Provides features like, manage current .env variables, and a handy backup functionality page


<br>

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

Some keys you may consider to be particularly sensitive and don't wish to expose them, even through this package. You can hide them through this interface:

```php
FilamentEnvEditorPlugin::make()
    ->hideKeys('APP_KEY', 'BCRYPT_ROUNDS')
```

### Authorization

If you would like to prevent certain users from accessing the logs page, you should add a `authorize` callback in the FilamentLEnvEditorPlugin chain.

```php
FilamentEnvEditorPlugin::make()
  ->authorize(
      fn () => auth()->user()->isAdmin()
  )
```

### Customizing the log page

To customize the "env-editor" page, you can extend the `ArabicSooq\FilamentEnvEditor\Pages\ViewEnv` page and override its methods.

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
  ->viewPage(CustomEnvPage::class)
```

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
