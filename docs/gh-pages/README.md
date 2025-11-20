# GitHub Pages 公開手順（orbit-docs-viewer 紹介サイト）

`docs/gh-pages/` 配下に、日本語版 `index.html` と英語版 `en/index.html` を配置しています。GitHub Pages のソースを `docs/` に指定するだけで公開できます。

## 手順

1. GitHub のリポジトリ Settings → Pages を開く  
2. **Source** を `Deploy from a branch`、**Branch** を `main` / `docs` に設定（docs フォルダを選択）  
3. 保存後、公開 URL（例: `https://<org>.github.io/<repo>/`）にアクセス  
   - 日本語: `/docs/gh-pages/`  
   - 英語: `/docs/gh-pages/en/`

## 内容

- `index.html`: 日本語版紹介ページ（スクリーンショット: `docs/images/top.ja.png`）
- `en/index.html`: 英語版紹介ページ（スクリーンショット: `docs/images/top.en.png`）
- `styles.css`: 両ページ共通のスタイル

## 開発時メモ

- 画像はリポジトリに含まれるものを参照しているため追加ビルド不要
- ベースパスは GitHub Pages のリポジトリパスに合わせて相対パスで構成済み（`/docs` ソース前提）
