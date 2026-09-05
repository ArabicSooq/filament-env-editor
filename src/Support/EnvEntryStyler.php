<?php

namespace ArabicSooq\FilamentEnvEditor\Support;

use GeoSot\EnvEditor\Dto\EntryObj;
use Illuminate\Support\Str;

/**
 * Presentation helpers that give the .env page its professional look:
 * deterministic color-coding per variable group and automatic masking of
 * sensitive values.
 */
class EnvEntryStyler
{
    /**
     * Map a variable key to a Filament color token.
     */
    public static function colorForKey(string $key): string
    {
        $key = Str::upper($key);

        return match (true) {
            Str::startsWith($key, 'APP_') => 'primary',
            Str::startsWith($key, 'DB_') => 'sky',
            Str::startsWith($key, ['MAIL_', 'SMTP_', 'IMAP_']) => 'violet',
            Str::startsWith($key, ['REDIS_', 'CACHE_', 'SESSION_']) => 'amber',
            Str::startsWith($key, ['QUEUE_', 'CONTRACT_', 'FILESYSTEM_']) => 'teal',
            Str::startsWith($key, ['LOG_', 'TELESCOPE_', 'HORIZON_']) => 'pink',
            self::looksLikeSensitiveKey($key) => 'rose',
            Str::startsWith($key, ['SCOUT_', 'BROADCAST_', 'VITE_', 'PUSHER_']) => 'indigo',
            default => 'gray',
        };
    }

    /**
     * Show the value inline, masking sensitive values unless the plugin
     * explicitly allows showing them.
     *
     * @param list<string> $revealKeys
     */
    public static function displayValue(EntryObj $entry, array $revealKeys = []): string
    {
        $key = Str::upper($entry->key);

        if (self::looksLikeSensitiveKey($key) && !in_array($entry->key, $revealKeys, true)) {
            $value = (string) $entry->getValue();

            if (blank($value)) {
                return '';
            }

            // Keep the shape (e.g. base64:...) but mask the payload.
            if (Str::contains($value, ':')) {
                [$prefix, $payload] = explode(':', $value, 2);

                return $prefix.':'.str_repeat('•', min(Str::length($payload), 12));
            }

            return str_repeat('•', min(Str::length($value), 12));
        }

        return (string) $entry->getValue();
    }

    public static function looksLikeSensitiveKey(string $key): bool
    {
        return (bool) preg_match('/(PASSWORD|PASSWD|SECRET|TOKEN)_|_TOKEN$|APP_KEY|PRIVATE|CREDENTIAL|AUTH/iu', $key)
            || (bool) preg_match('/^(DB_PASSWORD|MAIL_PASSWORD|REDIS_PASSWORD|AWS_SECRET_ACCESS_KEY|PUSHER_APP_SECRET)$/iu', $key);
    }
}
