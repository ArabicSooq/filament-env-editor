# محرر بيئة Filament

[![أحدث إصدار على Packagist](https://img.shields.io/packagist/v/arabicsooq/filament-env-editor.svg?style=flat-square)](https://packagist.org/packages/arabicsooq/filament-env-editor)
[![إجمالي التنزيلات](https://img.shields.io/packagist/dt/arabicsooq/filament-env-editor.svg?style=flat-square)](https://packagist.org/packages/arabicsooq/filament-env-editor)
[![الترخيص](https://img.shields.io/github/license/ArabicSooq/filament-env-editor?style=flat-square)](LICENSE.md)

إضافة بسيطة وقوية **لمحرر ملف `.env`** للوحات **Filament**.

توفر ميزات مثل إدارة متغيرات `.env` الحالية، مع صفحة مخصصة لنسخ الاحتياط **Backup** للحفاظ على بيئتك بأمان.

<br>

## ما الذي تقدمه هذه الحزمة؟

- **عرض وإدارة متغيرات `.env`** من داخل لوحة Filament مباشرةً دون الحاجة للوصول إلى الخادم.
- **إنشاء وتعديل وحذف** المفاتيح وتغيير قيمها من واجهة رسومية.
- **نسخ احتياطي كامل** لملف `.env` الحالي وإنشاء نسخ محفوظة قابلة للاستعادة.
- **استعادة أي نسخة احتياطية** بنقرة واحدة لإرجاع البيئة لحالتها السابقة.
- **تنزيل ورفع نسخ الاحتياط** وحفظ محتواها محليًا أو استيراده.
- **إمكانية إخفاء المفاتيح الحساسة** (مثل `APP_KEY`) حتى لا تظهر في الواجهة.
- **فحص أمني ذكي** بدرجة مباشرة، ونتائج تحذيرية وحرجة، مع اقتراحات قابلة للتنفيذ قبل إطلاق الموقع.
- **إخفاء القيم الحساسة افتراضيًا** مع إمكانية إظهارها لمفاتيح معينة فقط.
- **حماية التعديلات الضارة** — لا يمكن إفراغ أو حذف المفاتيح الحرجة، ويتم حظر القيم الخطرة، مع إمكانية حماية أنماط مثل `AWS_*`.
- **تخصيص مرن** لعناصر التنقّل والصفحة وسلطة الوصول عبر إعدادات المكوّن.
- متوافقة مع **Filament 5** و **Laravel 12/13**.

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

### إظهار القيم الحساسة المخفية

يتم إخفاء القيم الحساسة (كلمات المرور والأسرار والرموز) في الواجهة افتراضيًا. لإظهار بعضها كما هي، ضعها في قائمة المسموح:

```php
FilamentEnvEditorPlugin::make()
    ->revealKeys('MAIL_PASSWORD', 'SOME_TOKEN')
```

### حماية المفاتيح من الحذف

لا يمكن أبدًا إفراغ أو حذف المفاتيح الحرجة (`APP_KEY`, `APP_ENV`). كما يمكنك حماية مفاتيح إضافية — مع دعم البادئات الجامعة:

```php
FilamentEnvEditorPlugin::make()
    ->protectKeys('AWS_SECRET', 'MAIL_*')
```

كما تمنع الحزمة القيم التي تشبه أوامر الشل أو تحتوي على أحرف غير آمنة.

### تبويب الفحص الأمني

تبويب **الأمان** المخصص يقيس درجة ملف `.env` الحالي (من 0 إلى 100) ويعرض النتائج (الأسرار الفارغة، كلمات المرور الضعيفة، عناوين HTTP أو localhost في الإنتاج، تفعيل وضع التصحيح، `APP_KEY` غير صالح، برامج البريد المحلية في الإنتاج...) مع اقتراحات. وهو مفعّل افتراضيًا ويمكن تعطيله:

```php
FilamentEnvEditorPlugin::make()
    ->securityScan(false)
```

يمكنك أيضًا تمرير **closure** لتقييم الإعداد ديناميكيًا:

```php
FilamentEnvEditorPlugin::make()
    ->securityScan(fn () => app()->environment('local'))
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