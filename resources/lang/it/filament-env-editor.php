<?php

return [
    'navigation' => [
        'group' => 'System',
        'label' => 'Modifica .env',
    ],

    'page' => [
        'title' => 'Modifica .env',
    ],
    'tabs' => [
        'current-env' => [
            'title' => '.env Attivo',
        ],
        'security' => [
            'title' => 'Sicurezza',
        ],
        'backups' => [
            'title' => 'Backups',
        ],
    ],
    'security' => [
        'score' => 'Punteggio di sicurezza: :score / 100',
        'ok' => 'Nessun problema di sicurezza rilevato nel file .env corrente.',
        'status' => [
            'good' => 'Il tuo file .env sembra sano. Continua così.',
            'attention' => 'Alcune impostazioni meritano attenzione prima della messa in produzione.',
            'critical' => 'Il tuo file .env contiene problemi critici. Risolvili subito.',
        ],
        'severity' => [
            'info' => 'Info',
            'warning' => 'Avviso',
            'critical' => 'Critico',
        ],
        'findings' => [
            'emptyValue' => [
                'message' => 'La variabile :key è vuota.',
                'suggestion' => 'Inserisci un valore significativo o rimuovi la voce.',
            ],
            'dangerousValue' => [
                'message' => 'La variabile :key contiene un valore potenzialmente pericoloso (command injection).',
                'suggestion' => 'Rimuovi operatori di shell, backtick o nomi di comandi dal valore.',
            ],
            'weakSecret' => [
                'message' => 'La variabile :key utilizza una password debole molto nota.',
                'suggestion' => 'Genera un valore forte e unico.',
            ],
            'shortSecret' => [
                'message' => 'La variabile :key sembra troppo corta per essere un segreto.',
                'suggestion' => 'Usa almeno 8 caratteri casuali.',
            ],
            'insecureUrl' => [
                'message' => ':key usa HTTP semplice mentre l\'app è in :env.',
                'suggestion' => 'Passa a https:// in produzione.',
            ],
            'localhostUrl' => [
                'message' => ':key punta a localhost mentre l\'app è in produzione.',
                'suggestion' => 'Punta a un hostname o servizio pubblico.',
            ],
            'debugEnabled' => [
                'message' => 'La modalità debug è attiva mentre l\'app è in produzione.',
                'suggestion' => 'Imposta APP_DEBUG su false.',
            ],
            'emptyAppKey' => [
                'message' => 'APP_KEY è vuota.',
                'suggestion' => 'Esegui "php artisan key:generate".',
            ],
            'invalidAppKey' => [
                'message' => 'APP_KEY sembra non valida (dovrebbe essere una stringa base64:).',
                'suggestion' => 'Esegui "php artisan key:generate" e aggiorna il valore.',
            ],
            'noValidAppKey' => [
                'message' => 'Nessuna APP_KEY base64 valida trovata in produzione.',
                'suggestion' => 'Genera e imposta una APP_KEY valida prima della messa in produzione.',
            ],
            'productionMailDriver' => [
                'message' => 'La posta è impostata su un driver locale mentre l\'app è in produzione.',
                'suggestion' => 'Usa un vero trasporto di posta (smtp, ses, mailgun...).',
            ],
        ],
    ],
    'validation' => [
        'key' => [
            'required' => 'Il nome della chiave è obbligatorio.',
            'tooLong' => 'Il nome della chiave non deve superare i 100 caratteri.',
            'invalid' => 'Il nome della chiave può contenere solo lettere, numeri e underscore.',
        ],
        'value' => [
            'dangerous' => 'Questo valore non è consentito: sembra un comando o contiene caratteri non sicuri.',
            'suspiciousFlag' => 'Questo valore sembra un flag di riga di comando sospetto.',
            'cannotEmptyCritical' => 'La variabile :key è richiesta dal framework e non può essere svuotata qui.',
        ],
        'delete' => [
            'protected' => 'La variabile :key è protetta e non può essere eliminata.',
        ],
    ],
    'actions' => [
        'add' => [
            'title' => 'Aggiungi una nuova chiave',
            'modalHeading' => 'Aggiungi una nuova chiave',
            'success' => [
                'title' => 'Chiave ":Name", è stata aggiunta con successo',
            ],
            'form' => [
                'fields' => [
                    'key' => 'Chiave',
                    'value' => 'Valore',
                    'index' => 'Inserisci dopo la chiave (opzionale)',
                ],
                'helpText' => [
                    'index' => 'Se vuoi inserire questa nuova voce, dopo una esistente, puoi scegliere una delle chiavi esistenti',
                ],
            ],
        ],
        'edit' => [
            'tooltip' => 'Modifica Chiave ":name"',
            'modal' => [
                'text' => 'Modifica la chiave ":name"',
            ],
        ],
        'delete' => [
            'tooltip' => 'Rimuovi Chiave ":name"',
            'confirm' => [
                'title' => 'Sei sicuro di voler rimuovere la chiave ":name"?',
            ],
        ],
        'clear-cache' => [
            'title' => 'Pulisici Cache',
            'tooltip' => 'A volte laravel memorizza in cache le variabili ENV, quindi è necessario cancellare tutte le cache ("artisan optimize:clear"), per rileggere le modifice .env',
        ],

        'backup' => [
            'title' => 'Crea un backup',
            'success' => [
                'title' => 'Backup salvato con successo',
            ],
        ],
        'download' => [
            'title' => 'Scarica il .env attuale',
            'tooltip' => 'Scarica ":name" file di backup',
        ],
        'upload-backup' => [
            'title' => 'Carica un backup',
        ],
        'show-content' => [
            'modalHeading' => 'Contenuto del backup ":name"',
            'tooltip' => 'Mostra il contenuto del backup',
        ],
        'restore-backup' => [
            'confirm' => [
                'title' => 'Stai per ripristinare ":name", al posto del file ".env" attuale. Confermi la tua scelta?',
            ],
            'modalSubmit' => 'Ripristina',
            'tooltip' => 'Ripristina ":name", come ENV corrente',
        ],
        'delete-backup' => [
            'tooltip' => 'Rimuovi il backup ":name"',
            'confirm' => [
                'title' => 'Stai per rimuovere definitivamente il backup ":name". Sei sicuro di voler procedere con questa rimozione?',
            ],
        ],
    ],
];
