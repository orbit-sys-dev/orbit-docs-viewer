<?php

return [
    // モード（将来拡張用）
    'mode' => env('DOCS_VIEWER_MODE', 'laravel'),

    // docs ルート（base_path からの相対パス）
    'root_dir' => env('DOCS_VIEWER_ROOT_DIR', 'docs'),

    // 設計書カテゴリ用パス（base_path からの相対パス）
    'app_docs_path' => env('DOCS_VIEWER_APP_PATH', 'docs/app'),

    // Development グループごとのパスとラベル
    'development_groups' => [
        'memo' => [
            'label' => 'Memo',
            'path' => env('DOCS_VIEWER_DEV_MEMO_PATH', 'docs/development/memo'),
        ],
        'operation' => [
            'label' => 'Operation',
            'path' => env('DOCS_VIEWER_DEV_OPERATION_PATH', 'docs/development/operation'),
        ],
        'task' => [
            'label' => 'Task',
            'path' => env('DOCS_VIEWER_DEV_TASK_PATH', 'docs/development/task'),
        ],
    ],

    // README 例外パス（リポジトリトップ想定）
    'readme_relative' => env('DOCS_VIEWER_README', 'README.md'),

    // ルート設定
    'route' => [
        'prefix' => env('DOCS_VIEWER_ROUTE_PREFIX', 'dev/docs'),
        'name_prefix' => env('DOCS_VIEWER_ROUTE_NAME_PREFIX', 'dev.docs.'),
        'middleware' => array_values(array_filter(array_map('trim', explode(',', env('DOCS_VIEWER_MIDDLEWARE', 'web'))))),
    ],

    // レイアウト（アプリ側に寄生する場合は .env で差し替え）
    'layout' => env('DOCS_VIEWER_LAYOUT', 'docs-viewer::layouts.docs'),

    // ダッシュボードへの戻り先（null なら非表示）
    'dashboard_url' => env('DOCS_VIEWER_DASHBOARD_URL', null),

    // リンクラベル・URLのカスタマイズ
    'links' => [
        'dashboard' => [
            'label' => env('DOCS_VIEWER_DASHBOARD_LABEL', '開発ダッシュボードに戻る'),
            'url' => env('DOCS_VIEWER_DASHBOARD_URL', '/ops'),
        ],
        'home' => [
            'label' => env('DOCS_VIEWER_HOME_LABEL', 'トップページへ'),
            'url' => env('DOCS_VIEWER_HOME_URL', '/'),
        ],
    ],

    // タイムゾーン（null なら config('app.timezone')）
    'timezone' => env('DOCS_VIEWER_TIMEZONE', null),
];
