<?php

namespace ArabicSooq\FilamentEnvEditor\Security;

/**
 * A single security concern raised about an environment variable.
 */
class SecurityFinding
{
    public function __construct(
        public readonly string $key,
        public readonly SeverityLevel $level,
        public readonly string $message,
        public readonly ?string $suggestion = null,
    ) {
    }

    /**
     * @return array{key: string, level: string, message: string, suggestion: string|null}
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'level' => $this->level->value,
            'message' => $this->message,
            'suggestion' => $this->suggestion,
        ];
    }
}
