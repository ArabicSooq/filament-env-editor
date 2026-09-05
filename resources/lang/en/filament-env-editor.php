<?php

return [
    'navigation' => [
        'group' => 'System',
        'label' => '.Env Editor',
    ],

    'page' => [
        'title' => '.Env Editor',
    ],
    'tabs' => [
        'current-env' => [
            'title' => 'Current .env',
        ],
        'security' => [
            'title' => 'Security',
        ],
        'backups' => [
            'title' => 'Backups',
        ],
    ],
    'security' => [
        'score' => 'Security score: :score / 100',
        'ok' => '✅ No security concerns detected in the current environment file.',
        'status' => [
            'good' => 'Your environment file looks healthy. Keep it that way.',
            'attention' => 'Some settings deserve your attention before going live.',
            'critical' => 'Your environment file contains critical issues. Fix them now.',
        ],
        'severity' => [
            'info' => 'Info',
            'warning' => 'Warning',
            'critical' => 'Critical',
        ],
        'findings' => [
            'emptyValue' => [
                'message' => 'The :key variable is empty.',
                'suggestion' => 'Fill it with a meaningful value or remove the entry.',
            ],
            'dangerousValue' => [
                'message' => 'The :key variable contains a potentially dangerous value (command injection).',
                'suggestion' => 'Remove shell operators, backticks or command names from the value.',
            ],
            'weakSecret' => [
                'message' => 'The :key variable uses a well-known weak secret.',
                'suggestion' => 'Generate a strong, unique value.',
            ],
            'shortSecret' => [
                'message' => 'The :key variable looks short for a secret.',
                'suggestion' => 'Use at least 8 random characters.',
            ],
            'insecureUrl' => [
                'message' => ':key uses plain HTTP while the app is in :env.',
                'suggestion' => 'Switch to https:// in production.',
            ],
            'localhostUrl' => [
                'message' => ':key points to localhost while the app is in production.',
                'suggestion' => 'Point to a public hostname or service.',
            ],
            'debugEnabled' => [
                'message' => 'Debug mode is enabled while the app is in production.',
                'suggestion' => 'Set APP_DEBUG to false.',
            ],
            'emptyAppKey' => [
                'message' => 'APP_KEY is empty.',
                'suggestion' => 'Run "php artisan key:generate".',
            ],
            'invalidAppKey' => [
                'message' => 'APP_KEY looks invalid (should be a base64: string).',
                'suggestion' => 'Run "php artisan key:generate" and update the value.',
            ],
            'noValidAppKey' => [
                'message' => 'No valid base64 APP_KEY found in production.',
                'suggestion' => 'Generate and set a valid APP_KEY before going live.',
            ],
            'productionMailDriver' => [
                'message' => 'Mail is set to a local driver while the app is in production.',
                'suggestion' => 'Use a real mail transport (smtp, ses, mailgun...).',
            ],
        ],
    ],
    'validation' => [
        'key' => [
            'required' => 'A key name is required.',
            'tooLong' => 'The key name must not exceed 100 characters.',
            'invalid' => 'The key name may only contain letters, numbers and underscores.',
        ],
        'value' => [
            'dangerous' => 'This value is not allowed: it looks like a command or contains unsafe characters.',
            'suspiciousFlag' => 'This value looks like a suspicious command-line flag.',
            'cannotEmptyCritical' => 'The :key variable is required by the framework and can not be emptied here.',
        ],
        'delete' => [
            'protected' => 'The :key variable is protected and can not be deleted.',
        ],
    ],
    'actions' => [
        'add' => [
            'title' => 'Add new Entry',
            'modalHeading' => 'Add new Entry',
            'success' => [
                'title' => 'Key ":Name", was successfully written',
            ],
            'form' => [
                'fields' => [
                    'key' => 'key',
                    'value' => 'value',
                    'index' => 'Insert After existing key (optional)',
                ],
                'helpText' => [
                    'index' => 'In case you need to put this new entry, after an existing one, you may pick one of the existing key ',
                ],
            ],
        ],
        'edit' => [
            'tooltip' => 'Edit Entry ":name"',
            'modal' => [
                'text' => 'Edit Entry',
            ],
        ],
        'delete' => [
            'tooltip' => 'Remove the ":name" entry',
            'confirm' => [
                'title' => 'You are going to permanently remove ":name". Are you sure of this removal?',
            ],
        ],
        'clear-cache' => [
            'title' => 'Clear caches',
            'tooltip' => 'Sometimes laravel caches ENV variables, so you need to clear all caches ("artisan optimize:clear"), in order to rer-ead the .env change',
        ],

        'backup' => [
            'title' => 'Create a new Backup',
            'success' => [
                'title' => 'Backup, was successfully created',
            ],
        ],
        'download' => [
            'title' => 'Download current .env',
            'tooltip' => 'Download ":name" backup file',
        ],
        'upload-backup' => [
            'title' => 'Upload a backup file',
        ],
        'show-content' => [
            'modalHeading' => 'Raw content of ":name" backup',
            'tooltip' => 'Show raw content',
        ],
        'restore-backup' => [
            'confirm' => [
                'title' => 'You are going to restore ":name", in place of current ".env" file. Please confirm you choice',
            ],
            'modalSubmit' => 'Restore',
            'tooltip' => 'Restore ":name", as current ENV',
        ],
        'delete-backup' => [
            'tooltip' => 'Remove the ":name" backup file',
            'confirm' => [
                'title' => 'You are going to permanently remove ":name" backup file. Are you sure of this removal?',
            ],
        ],
    ],
];
