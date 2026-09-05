# محرر بيئة Filament

[![أحدث إصدار على Packagist](https://img.shields.io/packagist/v/arabicsooq/filament-env-editor.svg?style=flat-square)](https://packagist.org/packages/arabicsooq/filament-env-editor)
[![إجمالي التنزيلات](https://img.shields.io/packagist/dt/arabicsooq/filament-env-editor.svg?style=flat-square)](https://packagist.org/packages/arabicsooq/filament-env-editor)
[![الترخيص](https://img.shields.io/github/license/ArabicSooq/filament-env-editor?style=flat-square)](LICENSE.md)

<div align="center">
    <img src="https://github.com/GeoSot/filament-env-editor/assets/22406063/e53b56d9-3e2d-4943-b1bd-4b18b6d5fc15" alt="لافتة" style="width: 100%; max-width: 800px; border-radius: 10px" />
</div>

إضافة بسيطة وقوية **لمحرر ملف `.env`** للوحات **Filament**.

توفر ميزات مثل إدارة متغيرات `.env` الحالية، مع صفحة مخصصة لنسخ الاحتياط **Backup** للحفاظ على بيئتك بأمان.

<br>

## نبذة عن هذه الحزمة

هذه الحزمة هي **نسخة مطوّرة ومُعاد بناؤها** من المشروع الممتاز
[GeoSot/filament-env-editor](https://github.com/GeoSot/filament-env-editor).

قمنا بدمج أفضل ما في العالمين:

- أحدث كود **Filament 5** (Schema API، تدفّق تحديث محسّن، واجهة أفضل) من المستودع الأصلي.
- التوثيق **العربي** ودعم **Laravel 12/13** الذي أُضيف في فرع `ArabicSooq`.
- إصلاح العديد من الأخطاء التي كانت موجودة في الفرع القديم (تحديث الصفحة المعطّل، مفاتيح مكررة، إمكانية تعديل اسم المفتاح، نقص تأكيد النجاح/الفشل).

تبقى الحزمة **مستقلة تمامًا** ولا تعتمد على أي كود خاص بالتطبيق.

> **حقوق الملكية:** كل الفضل في الحزمة الأصلية يعود إلى [Geo Sot](https://github.com/GeoSot). هذا الفرع يضيف فقط الصيانة والإصلاحات ودعم لغات إضافية.

<br>

## المتطلبات

| المكوّن | الإصدار |
|---------|---------|
| PHP | `^8.2` |
| Laravel | `^12.0 \| ^13.0` |
| Filament | `~5.0` |

## التثبيت

يمكنك تثبيت الحزمة عبر **Composer**:

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

### تخصيص عنصر التنقّل

```php
FilamentEnvEditorPlugin::make()
    ->navigationGroup('أدوات النظام')
    ->navigationLabel('بيئتي')
    ->navigationIcon('heroicon-o-cog-8-tooth')
    ->navigationSort(1)
    ->slug('env-editor')
```

### إخفاء المفاتيح

بعض المفاتيح قد تكون حساسة ولا ترغب في عرضها حتى من خلال هذه الحزمة. يمكنك إخفاؤها عبر هذه الواجهة:

```php
FilamentEnvEditorPlugin::make()
    ->hideKeys('APP_KEY', 'BCRYPT_ROUNDS')
```

### التفويض

إذا كنت ترغب في منع بعض المستخدمين من الوصول لصفحة التحرير، أضف **callback authorize** ضمن سلسلة `FilamentEnvEditorPlugin`:

```php
FilamentEnvEditorPlugin::make()
    ->authorize(
        fn () => auth()->user()->isAdmin()
    )
```

### تخصيص صفحة التحرير

لتخصيص صفحة "env-editor"، يمكنك تمديد `ArabicSooq\FilamentEnvEditor\Pages\ViewEnv` وتعديل دوالها:

```php
use ArabicSooq\FilamentEnvEditor\Pages\ViewEnv as BaseViewEnvEditor;

class ViewEnv extends BaseViewEnvEditor
{
    // الكود الخاص بك
}
```

ثم حدّد الصفحة المخصصة في إعدادات المكوّن:

```php
use App\Filament\Pages\ViewEnv;

FilamentEnvEditorPlugin::make()
    ->viewPage(ViewEnv::class)
```

## الاختبار

```bash
composer install
composer test        # تشغيل PHPUnit
composer phpstan     # التحليل الثابت (PHPStan)
composer cs          # نمط الكود (PHP-CS-Fixer)
```

## المساهمة

يرجى مراجعة [إرشادات المساهمة](.github/CONTRIBUTING.md) لمزيد من التفاصيل.

## الترخيص

هذه الحزمة مرخّصة تحت **MIT License**. لمزيد من المعلومات، يرجى مراجعة [ملف الترخيص](LICENSE.md).
