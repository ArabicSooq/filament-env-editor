<?php

namespace ArabicSooq\FilamentEnvEditor\Security;

use GeoSot\EnvEditor\Dto\EntryObj;
use GeoSot\EnvEditor\Facades\EnvEditor;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Scans the current environment file and reports security concerns.
 *
 * The scanner is intentionally local and dependency-free: it only inspects
 * the key/value pairs the underlying EnvEditor library exposes, and it never
 * executes or resolves the values it inspects.
 */
class EnvSecurityChecker
{
    /**
     * Values considered weak for sensitive keys (passwords, secrets, tokens).
     *
     * @var list<string>
     */
    protected const WEAK_SECRET_VALUES = [
        'password',
        'secret',
        'changeme',
        'change-me',
        'change_me',
        'admin',
        '123456',
        '12345678',
        'qwerty',
        'letmein',
    ];

    /**
     * Suggests that a variable name holds sensitive material.
     */
    protected const SENSITIVE_KEY_PATTERN = '/(PASSWORD|PASSWD|SECRET|TOKEN|API[_]?KEY|APP_KEY|PRIVATE|CREDENTIAL|AUTH)/i';

    /**
     * Suspicious constructs that have no legitimate place inside a .env value.
     */
    protected const DANGEROUS_VALUE_PATTERN = '/\$\(|`|;\s*(rm|curl|wget|nc|mkfifo|sh|bash|python|perl|php)\b|&&\s*(rm|curl|wget)\b|\|\s*(sh|bash)\b/i';

    /**
     * Run the audit against the current .env and return all findings.
     *
     * @return Collection<int, SecurityFinding>
     */
    public function scan(): Collection
    {
        $entries = EnvEditor::getEnvFileContent()
            ->reject(fn (EntryObj $obj): bool => $obj->isSeparator());

        $findings = new Collection();

        foreach ($entries as $entry) {
            $findings = $findings->merge($this->inspectEntry($entry, $entries));
        }

        $findings = $findings->merge($this->inspectAppWide($entries));

        return $findings
            ->sortByDesc(fn (SecurityFinding $finding): int => $finding->level->weight())
            ->sortBy('key')
            ->values();
    }

    /**
     * @param Collection<int, EntryObj> $entries
     *
     * @return Collection<int, SecurityFinding>
     */
    protected function inspectEntry(EntryObj $entry, Collection $entries): Collection
    {
        $key = $entry->key;
        $value = (string) $entry->getValue();
        $findings = new Collection();

        if (blank($value)) {
            $findings->push(new SecurityFinding(
                $key,
                SeverityLevel::Warning,
                __('filament-env-editor::filament-env-editor.security.findings.emptyValue.message', ['key' => $key]),
                __('filament-env-editor::filament-env-editor.security.findings.emptyValue.suggestion'),
            ));
        }

        if (!blank($value) && preg_match(self::DANGEROUS_VALUE_PATTERN, $value)) {
            $findings->push(new SecurityFinding(
                $key,
                SeverityLevel::Critical,
                __('filament-env-editor::filament-env-editor.security.findings.dangerousValue.message', ['key' => $key]),
                __('filament-env-editor::filament-env-editor.security.findings.dangerousValue.suggestion'),
            ));
        }

        if ($this->looksLikeSensitiveKey($key)) {
            $findings = $findings->merge($this->inspectSensitiveKey($entry));
        }

        if (Str::startsWith(Str::lower($key), 'http') || Str::endsWith(Str::upper($key), 'URL')) {
            $findings = $findings->merge($this->inspectUrlValue($entry, $entries));
        }

        if ('APP_DEBUG' === Str::upper($key)) {
            $findings = $findings->merge($this->inspectAppDebug($entry, $entries));
        }

        if ('APP_KEY' === Str::upper($key)) {
            $findings = $findings->merge($this->inspectAppKey($entry));
        }

        return $findings;
    }

    /**
     * @return Collection<int, SecurityFinding>
     */
    protected function inspectSensitiveKey(EntryObj $entry): Collection
    {
        $findings = new Collection();

        $key = $entry->key;
        $value = (string) $entry->getValue();

        if (blank($value)) {
            return $findings;
        }

        foreach (self::WEAK_SECRET_VALUES as $weak) {
            if (Str::lower($value) === $weak) {
                $findings->push(new SecurityFinding(
                    $key,
                    SeverityLevel::Critical,
                    __('filament-env-editor::filament-env-editor.security.findings.weakSecret.message', ['key' => $key]),
                    __('filament-env-editor::filament-env-editor.security.findings.weakSecret.suggestion'),
                ));

                return $findings;
            }
        }

        if (Str::length($value) < 8) {
            $findings->push(new SecurityFinding(
                $key,
                SeverityLevel::Warning,
                __('filament-env-editor::filament-env-editor.security.findings.shortSecret.message', ['key' => $key]),
                __('filament-env-editor::filament-env-editor.security.findings.shortSecret.suggestion'),
            ));
        }

        return $findings;
    }

    /**
     * @param Collection<int, EntryObj> $entries
     *
     * @return Collection<int, SecurityFinding>
     */
    protected function inspectUrlValue(EntryObj $entry, Collection $entries): Collection
    {
        $findings = new Collection();

        $value = (string) $entry->getValue();
        $key = $entry->key;

        if (!Str::startsWith(Str::lower($value), ['http://', 'https://'])) {
            return $findings;
        }

        $appEnvEntry = $entries->firstWhere('key', 'APP_ENV');
        $isProduction = $appEnvEntry instanceof EntryObj
            && 'production' === Str::lower((string) $appEnvEntry->getValue());

        if ($isProduction && Str::startsWith(Str::lower($value), 'http://')) {
            $findings->push(new SecurityFinding(
                $key,
                SeverityLevel::Warning,
                __('filament-env-editor::filament-env-editor.security.findings.insecureUrl.message', ['key' => $key, 'env' => 'production']),
                __('filament-env-editor::filament-env-editor.security.findings.insecureUrl.suggestion'),
            ));
        }

        if (Str::contains(Str::lower($value), ['localhost', '127.0.0.1']) && $isProduction) {
            $findings->push(new SecurityFinding(
                $key,
                SeverityLevel::Warning,
                __('filament-env-editor::filament-env-editor.security.findings.localhostUrl.message', ['key' => $key]),
                __('filament-env-editor::filament-env-editor.security.findings.localhostUrl.suggestion'),
            ));
        }

        return $findings;
    }

    /**
     * @param Collection<int, EntryObj> $entries
     *
     * @return Collection<int, SecurityFinding>
     */
    protected function inspectAppDebug(EntryObj $entry, Collection $entries): Collection
    {
        $findings = new Collection();

        $value = Str::lower((string) $entry->getValue());

        if (!in_array($value, ['true', '1', '(true)', '(1)'])) {
            return $findings;
        }

        $appEnvEntry = $entries->firstWhere('key', 'APP_ENV');
        $appEnv = $appEnvEntry instanceof EntryObj
            ? Str::lower((string) $appEnvEntry->getValue())
            : '';

        if (in_array($appEnv, ['production', 'prod'])) {
            $findings->push(new SecurityFinding(
                'APP_DEBUG',
                SeverityLevel::Critical,
                __('filament-env-editor::filament-env-editor.security.findings.debugEnabled.message'),
                __('filament-env-editor::filament-env-editor.security.findings.debugEnabled.suggestion'),
            ));
        }

        return $findings;
    }

    /**
     * @return Collection<int, SecurityFinding>
     */
    protected function inspectAppKey(EntryObj $entry): Collection
    {
        $findings = new Collection();

        $value = (string) $entry->getValue();

        if (blank($value)) {
            $findings->push(new SecurityFinding(
                'APP_KEY',
                SeverityLevel::Critical,
                __('filament-env-editor::filament-env-editor.security.findings.emptyAppKey.message'),
                __('filament-env-editor::filament-env-editor.security.findings.emptyAppKey.suggestion'),
            ));

            return $findings;
        }

        if (!Str::startsWith($value, 'base64:') || Str::length($value) < 30) {
            $findings->push(new SecurityFinding(
                'APP_KEY',
                SeverityLevel::Critical,
                __('filament-env-editor::filament-env-editor.security.findings.invalidAppKey.message'),
                __('filament-env-editor::filament-env-editor.security.findings.invalidAppKey.suggestion'),
            ));
        }

        return $findings;
    }

    /**
     * @param Collection<int, EntryObj> $entries
     *
     * @return Collection<int, SecurityFinding>
     */
    protected function inspectAppWide(Collection $entries): Collection
    {
        $findings = new Collection();

        $appEnvEntry = $entries->firstWhere('key', 'APP_ENV');
        $appEnv = $appEnvEntry instanceof EntryObj
            ? Str::lower((string) $appEnvEntry->getValue())
            : '';

        if ('production' === $appEnv && $entries->contains(fn (EntryObj $entry): bool => 'MAIL_MAILER' === Str::upper($entry->key) && in_array(Str::lower((string) $entry->getValue()), ['log', 'array']))) {
            $findings->push(new SecurityFinding(
                'MAIL_MAILER',
                SeverityLevel::Warning,
                __('filament-env-editor::filament-env-editor.security.findings.productionMailDriver.message'),
                __('filament-env-editor::filament-env-editor.security.findings.productionMailDriver.suggestion'),
            ));
        }

        if ('production' === $appEnv && !$entries->contains(fn (EntryObj $entry): bool => 'APP_KEY' === Str::upper($entry->key) && Str::startsWith((string) $entry->getValue(), 'base64:'))) {
            $findings->push(new SecurityFinding(
                'APP_KEY',
                SeverityLevel::Critical,
                __('filament-env-editor::filament-env-editor.security.findings.noValidAppKey.message'),
                __('filament-env-editor::filament-env-editor.security.findings.noValidAppKey.suggestion'),
            ));
        }

        return $findings;
    }

    /**
     * Compute a 0-100 score based on the current findings.
     */
    public function score(): int
    {
        $score = 100;
        $critical = 0;
        $warning = 0;

        foreach ($this->scan() as $finding) {
            match ($finding->level) {
                SeverityLevel::Critical => $critical++,
                SeverityLevel::Warning => $warning++,
                SeverityLevel::Info => null,
            };
        }

        $score -= $critical * 25;
        $score -= $warning * 8;

        return max(0, min(100, $score));
    }

    public function isSecure(): bool
    {
        return $this->scan()->doesntContain(fn (SecurityFinding $finding): bool => SeverityLevel::Critical === $finding->level);
    }

    protected function looksLikeSensitiveKey(string $key): bool
    {
        return (bool) preg_match(self::SENSITIVE_KEY_PATTERN, $key);
    }
}
