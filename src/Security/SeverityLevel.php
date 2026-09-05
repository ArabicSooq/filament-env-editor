<?php

namespace ArabicSooq\FilamentEnvEditor\Security;

/**
 * Severity levels used by the security scanner and the smart validation guard.
 */
enum SeverityLevel: string
{
    case Info = 'info';
    case Warning = 'warning';
    case Critical = 'critical';

    /**
     * Map the level to a Filament color name so badges render in a
     * consistent, professional palette.
     */
    public function color(): string
    {
        return match ($this) {
            self::Info => 'info',
            self::Warning => 'warning',
            self::Critical => 'danger',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Info => __('filament-env-editor::filament-env-editor.security.severity.info'),
            self::Warning => __('filament-env-editor::filament-env-editor.security.severity.warning'),
            self::Critical => __('filament-env-editor::filament-env-editor.security.severity.critical'),
        };
    }

    public function weight(): int
    {
        return match ($this) {
            self::Info => 1,
            self::Warning => 2,
            self::Critical => 3,
        };
    }
}
