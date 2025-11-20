# 設計書（App Docs）の使い方

`docs/app/` 配下に設計書を配置し、アプリケーションのディレクトリ構成と可能な限り同一のパスで「ニアドキュメント」として運用するガイドです。Laravel では `app/` 配下のパス構造をそのまま `docs/app/` に写し、クラスや機能の近くに対応するドキュメントを置くと参照しやすくなります。

## 推奨構成（Laravel 例）

- アプリ本体: `app/Services/FooService.php`  
  設計書: `docs/app/Services/FooService.md`
- アプリ本体: `app/Http/Controllers/Api/BarController.php`  
  設計書: `docs/app/Http/Controllers/Api/BarController.md`
- 共通仕様や概要: `docs/app/README.md` または `docs/app/_index.md`

このように「同一パス + 拡張子だけ Markdown」にすることで、コードとドキュメントを往復しやすくなり、ルートやクラス名とファイルパスの対応を自然に記憶できます。

## 配置と命名のポイント

- できるだけコードと同じパス階層を保つ（例: `app/Domain/...` → `docs/app/Domain/...`）。
- クラス単位の詳細は同名 `.md` ファイルに切り出し、モジュール共通の概要はディレクトリ直下の `README.md` に集約する。
- 設計意図、主要メソッドの振る舞い、外部依存、テスト方針を簡潔に記載する。更新頻度の高い TODO は `docs/development/` 側にメモとして分離し、設計書は確定事項を中心に保つ。

## Docs Viewer での表示

本パッケージの App カテゴリは `config('docs_viewer.app_docs_path')`（デフォルト `docs/app`）を対象にしており、`/dev/docs/app` で一覧表示できます。ディレクトリ構造を保ったまま配置すれば、そのままツリーを走査してリンクを生成します。

## 運用ヒント

- 設計書の更新と同時に該当コードのレビューを実施し、差分を最小化する。レビューが必要な場合は `docs/development/task/` に計画を残す。
- 大きな仕様変更時は、旧仕様を `history/` や日付付きファイルに退避し、現行仕様を常に最新に保つ。
- 変更後は Docs Viewer でリンク切れがないか `/dev/docs/app` を確認する。
