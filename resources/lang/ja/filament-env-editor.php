<?php

return [
    'navigation' => [
        'group' => 'システム',
        'label' => '.Env エディター',
    ],

    'page' => [
        'title' => '.Env エディター',
    ],
    'tabs' => [
        'current-env' => [
            'title' => '現状の.env',
        ],
        'security' => [
            'title' => 'セキュリティ',
        ],
        'backups' => [
            'title' => 'バックアップ',
        ],
    ],
    'security' => [
        'score' => 'セキュリティスコア: :score / 100',
        'ok' => '現在の .env ファイルにセキュリティ上の問題は検出されませんでした。',
        'status' => [
            'good' => '.env ファイルは健全です。この状態を維持してください。',
            'attention' => '本番公開の前に注意すべき設定があります。',
            'critical' => '.env ファイルに重大な問題があります。今すぐ修正してください。',
        ],
        'severity' => [
            'info' => '情報',
            'warning' => '警告',
            'critical' => '重大',
        ],
        'findings' => [
            'emptyValue' => [
                'message' => ':key 変数が空です。',
                'suggestion' => '意味のある値を設定するか、エントリーを削除してください。',
            ],
            'dangerousValue' => [
                'message' => ':key 変数には危険な可能性のある値（コマンドインジェクション）が含まれています。',
                'suggestion' => '値からシェル演算子、バッククォート、コマンド名を削除してください。',
            ],
            'weakSecret' => [
                'message' => ':key 変数は既知の弱い秘密値を使用しています。',
                'suggestion' => '強力で一意な値を生成してください。',
            ],
            'shortSecret' => [
                'message' => ':key 変数は秘密値としては短すぎるようです。',
                'suggestion' => '8文字以上のランダムな文字列を使用してください。',
            ],
            'insecureUrl' => [
                'message' => 'アプリが :env のときに :key が平文の HTTP を使用しています。',
                'suggestion' => '本番環境では https:// に切り替えてください。',
            ],
            'localhostUrl' => [
                'message' => 'アプリが本番環境のときに :key が localhost を指しています。',
                'suggestion' => '公開ホスト名またはサービスを指定してください。',
            ],
            'debugEnabled' => [
                'message' => 'アプリが本番環境なのにデバッグモードが有効です。',
                'suggestion' => 'APP_DEBUG を false に設定してください。',
            ],
            'emptyAppKey' => [
                'message' => 'APP_KEY が空です。',
                'suggestion' => '"php artisan key:generate" を実行してください。',
            ],
            'invalidAppKey' => [
                'message' => 'APP_KEY が無効のようです（base64: 文字列である必要があります）。',
                'suggestion' => '"php artisan key:generate" を実行して値を更新してください。',
            ],
            'noValidAppKey' => [
                'message' => '本番環境で有効な base64 APP_KEY が見つかりません。',
                'suggestion' => '本番公開前に有効な APP_KEY を生成して設定してください。',
            ],
            'productionMailDriver' => [
                'message' => 'アプリが本番環境なのにメールがローカルドライバーに設定されています。',
                'suggestion' => '実際のメールトランスポート（smtp、ses、mailgun など）を使用してください。',
            ],
        ],
    ],
    'validation' => [
        'key' => [
            'required' => 'キー名は必須です。',
            'tooLong' => 'キー名は100文字を超えてはなりません。',
            'invalid' => 'キー名には英数字とアンダースコアのみ使用できます。',
        ],
        'value' => [
            'dangerous' => 'この値は許可されていません: コマンドのように見えるか、安全でない文字が含まれています。',
            'suspiciousFlag' => 'この値は不審なコマンドラインオプションのように見えます。',
            'cannotEmptyCritical' => ':key 変数はフレームワークに必要であり、ここでは空にできません。',
        ],
        'delete' => [
            'protected' => ':key 変数は保護されており、削除できません。',
        ],
    ],
    'actions' => [
        'add' => [
            'title' => '新規エントリーの追加',
            'modalHeading' => '新規エントリーの追加',
            'success' => [
                'title' => 'キー ":Name", が正常に書き込まれました',
            ],
            'form' => [
                'fields' => [
                    'key' => 'キー',
                    'value' => '値',
                    'index' => '既存のキーの後に挿入（オプション）',
                ],
                'helpText' => [
                    'index' => '既存のエントリーの後に新しいエントリーを追加する必要がある場合は、既存のキーの1つを選択できます',
                ],
            ],
        ],
        'edit' => [
            'tooltip' => 'エントリー　":name"　の編集',
            'modal' => [
                'text' => 'エントリーの編集',
            ],
        ],
        'delete' => [
            'tooltip' => '":name" エントリーの削除',
            'confirm' => [
                'title' => '":name" を完全に削除します。本当に削除されますか？',
            ],
        ],
        'clear-cache' => [
            'title' => 'キャッシュのクリア',
            'tooltip' => '時々、Laravel は ENV 変数をキャッシュするため、.env の変更を再読み込みするためにすべてのキャッシュをクリアする必要があります',
        ],

        'backup' => [
            'title' => '新しいバックアップの作成',
            'success' => [
                'title' => 'バックアップが正常に作成されました',
            ],
        ],
        'download' => [
            'title' => 'バックアップのダウンロード',
            'tooltip' => '":name" バックアップファイルをダウンロード',
        ],
        'upload-backup' => [
            'title' => 'バックアップファイルのアップロード',
        ],
        'show-content' => [
            'modalHeading' => '":name" の詳細を確認',
            'tooltip' => '詳細を表示',
        ],
        'restore-backup' => [
            'confirm' => [
                'title' => '":name" を現在の ENV として復元しようとしています。本当に復元しますか？',
            ],
            'modalSubmit' => '復元',
            'tooltip' => '":name" を現在の ENV として復元',
        ],
        'delete-backup' => [
            'tooltip' => '":name" バックアップファイルの削除',
            'confirm' => [
                'title' => '":name" バックアップファイルを完全に削除します。本当に削除しますか？',
            ],
        ],
    ],
];
