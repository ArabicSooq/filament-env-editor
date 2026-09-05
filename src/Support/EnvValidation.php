<?php

namespace ArabicSooq\FilamentEnvEditor\Support;

use GeoSot\EnvEditor\Dto\EntryObj;
use GeoSot\EnvEditor\Facades\EnvEditor;
use Illuminate\Support\Str;

/**
 * Guards against harmful modifications to the environment file.
 *
 * All checks are pure string inspection: nothing is executed and nothing
 * is written. Use the returned messages to halt an action and inform the
 * user before any write happens.
 */
class EnvValidation
{
    /**
     * Keys that must never be deleted or empty - the application cannot
     * boot without them.
     *
     * @var list<string>
     */
    public const CRITICAL_KEYS = [
        'APP_KEY',
        'APP_ENV',
    ];

    protected const KEY_NAME_PATTERN = '/^[A-Z0-9_]+$/i';

    /**
     * Values that should never be stored into the environment.
     */
    protected const FORBIDDEN_VALUE_PATTERN = '/\$\(|`|;\s*(rm|curl|wget|nc|mkfifo|sh|bash)\b|&&\s*(rm|curl|wget)\b|\|\s*(sh|bash)\b|\r|\n/i';

    /**
     * Validate that a key name is safe to add.
     */
    public static function validateKeyName(string $key): ?string
    {
        $key = trim($key);

        if (blank($key)) {
            return __('filament-env-editor::filament-env-editor.validation.key.required');
        }

        if (Str::length($key) > 100) {
            return __('filament-env-editor::filament-env-editor.validation.key.tooLong');
        }

        if (!preg_match(self::KEY_NAME_PATTERN, $key)) {
            return __('filament-env-editor::filament-env-editor.validation.key.invalid');
        }

        return null;
    }

    /**
     * Validate a value before it is written. Returns an error message,
     * or null when the value is acceptable.
     */
    public static function validateValue(string $key, mixed $value): ?string
    {
        if (null === $value) {
            return null;
        }

        $value = (string) $value;

        if (preg_match(self::FORBIDDEN_VALUE_PATTERN, $value)) {
            return __('filament-env-editor::filament-env-editor.validation.value.dangerous');
        }

        if (Str::startsWith($value, '-') && Str::contains($value, 'x')) {
            return __('filament-env-editor::filament-env-editor.validation.value.suspiciousFlag');
        }

        return null;
    }

    /**
     * Ensure that a sensitive key can not be emptied or cleared.
     */
    public static function canEmptyKey(string $key): bool
    {
        return !in_array(Str::upper(trim($key)), self::CRITICAL_KEYS, true);
    }

    /**
     * Ensure the key can not be deleted at all.
     *
     * @param list<string> $protectedKeys
     */
    public static function canDeleteKey(string $key, array $protectedKeys = []): bool
    {
        $key = Str::upper(trim($key));

        $forbidden = array_merge(self::CRITICAL_KEYS, $protectedKeys);

        if (in_array($key, $forbidden, true)) {
            return false;
        }

        // Wildcard protection: "AWS_*", "MAIL_*", ... protect any prefix.
        foreach ($protectedKeys as $protectedKey) {
            if (Str::endsWith($protectedKey, '*') && Str::startsWith($key, Str::beforeLast($protectedKey, '*'))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check whether a proposed value would weaken an existing sensitive key.
     */
    public static function weakensKey(string $key, mixed $proposedValue, mixed $currentValue): bool
    {
        $key = Str::upper(trim($key));

        if (!self::canEmptyKey($key)) {
            return blank($proposedValue);
        }

        if (blank($proposedValue)) {
            return filled($currentValue);
        }

        return false;
    }

    /**
     * Collect every entry that is protected from deletion.
     *
     * @param list<string> $protectedKeys
     *
     * @return list<string>
     */
    public static function protectedKeys(array $protectedKeys = []): array
    {
        return EnvEditor::getEnvFileContent()
            ->reject(fn (EntryObj $obj): bool => $obj->isSeparator())
            ->map(fn (EntryObj $obj): string => $obj->key)
            ->filter(fn (string $key): bool => !self::canDeleteKey($key, $protectedKeys))
            ->values()
            ->all();
    }
}
