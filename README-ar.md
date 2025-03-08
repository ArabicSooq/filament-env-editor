# محرر بيئة Filament

[![أحدث إصدار على Packagist](https://img.shields.io/packagist/v/arabicsooq/filament-env-editor.svg?style=flat-square)](https://packagist.org/packages/arabicsooq/filament-env-editor)  
[![إجمالي التنزيلات](https://img.shields.io/packagist/dt/arabicsooq/filament-env-editor.svg?style=flat-square)](https://packagist.org/packages/arabicsooq/filament-env-editor)  

<p align="center">
    <img src="https://github.com/GeoSot/filament-env-editor/assets/22406063/e53b56d9-3e2d-4943-b1bd-4b18b6d5fc15" alt="لافتة" style="width: 100%; max-width: 800px; border-radius: 10px" />
</p>

إضافة بسيطة لعرض ملف `.env` داخل لوحات **Filament**،  
توفر ميزات مثل إدارة المتغيرات الحالية في `.env`، بالإضافة إلى صفحة مخصصة للنسخ الاحتياطي.

<br>

## إشعار مهم: المؤلف الأصلي ودعم Laravel 12

هذا المستودع هو نسخة معدلة من [GeoSot/filament-env-editor](https://github.com/GeoSot/filament-env-editor) مع تعديلات لدعم **Laravel 12**.  
المؤلف الأصلي للحزمة هو [GeoSot](https://github.com/GeoSot).

تم إجراء هذه التعديلات لضمان التوافق مع أحدث إصدارات Laravel، مما يسمح للمستخدمين بالاستفادة من هذه الأداة القيمة في بيئة Laravel 12.

<br>

## التثبيت

يمكنك تثبيت الحزمة باستخدام **Composer**:

```bash
composer require arabicsooq/filament-env-editor
```

## الاستخدام

أضف `ArabicSooq\FilamentEnvEditor\FilamentEnvEditorPlugin` إلى إعدادات اللوحة الخاصة بك:

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

## الإعدادات

### تخصيص عنصر التنقل

```php
FilamentEnvEditorPlugin::make()
    ->navigationGroup('أدوات النظام')
    ->navigationLabel('بيئتي')
    ->navigationIcon('heroicon-o-cog-8-tooth')
    ->navigationSort(1)
    ->slug('env-editor')
```

### إخفاء المفاتيح

بعض المفاتيح قد تكون حساسة ولا ترغب في عرضها حتى من خلال هذه الحزمة. يمكنك إخفاؤها عبر الواجهة التالية:

```php
FilamentEnvEditorPlugin::make()
    ->hideKeys('APP_KEY', 'BCRYPT_ROUNDS')
```

### التفويض والصلاحيات

إذا كنت ترغب في منع بعض المستخدمين من الوصول إلى صفحة التعديل، يمكنك إضافة **callback authorize** ضمن سلسلة `FilamentEnvEditorPlugin`:

```php
FilamentEnvEditorPlugin::make()
  ->authorize(
      fn () => auth()->user()->isAdmin()
  )
```

### تخصيص صفحة التحرير

لتخصيص صفحة "env-editor"، يمكنك توسيع `ArabicSooq\FilamentEnvEditor\Pages\ViewEnv` وتعديل وظائفها:

```php
use ArabicSooq\FilamentEnvEditor\Pages\ViewEnv as BaseViewEnvEditor;

class ViewEnv extends BaseViewEnvEditor
{
    // الكود الخاص بك
}
```

ثم تحديد الصفحة المخصصة في إعدادات المكون:

```php
use App\Filament\Pages\ViewEnv;

FilamentEnvEditorPlugin::make()
  ->viewPage(CustomEnvPage::class)
```

## المساهمة

يرجى مراجعة [إرشادات المساهمة](.github/CONTRIBUTING.md) لمزيد من التفاصيل.

## الرخصة

هذه الحزمة مرخصة تحت **MIT License**. لمزيد من المعلومات، يرجى مراجعة [ملف الترخيص](LICENSE.md).
