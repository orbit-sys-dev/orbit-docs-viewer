# Contributing Guidelines

Thank you for considering a contribution. This repository uses English as the default language for issues and pull requests. A Japanese version is available at [CONTRIBUTING.ja.md](./CONTRIBUTING.ja.md).

## Getting Started
- Fork or create a feature branch from `main`. Avoid committing directly to `main`.
- Requirements: PHP 8.2+, Composer. Frontend assets are not bundled in this repo.
- Install dependencies: `composer install`.

## Development Workflow
- Branch naming: use a descriptive prefix (e.g., `feature/`, `fix/`, `docs/`) and ASCII only.
- Formatting: run `vendor/bin/pint` before opening a PR.
- Tests: run available tests if present (e.g., `composer test`). If tests are missing, double-check changes manually.
- Documentation: update relevant docs when behavior or public API changes.

## Commits and Pull Requests
- Commits: short, imperative messages (e.g., “Add docs viewer config”).
- PRs: include purpose, scope, any UI changes (with screenshots if applicable), and test results or manual check notes.
- Do not include secrets or real credentials. `.env` files must not be committed.

## Security
- If you believe you’ve found a security issue, do not open a public issue. Contact the maintainers privately if possible.

## License
- By contributing, you agree that your contributions are licensed under the MIT License for this repository.

## Documentation Guidelines

### Languages

- The default language for this project is **English**.
- All new documentation **must** be written in English first.
- Japanese documentation is provided as an optional, complementary translation.

### File structure

We use the following structure for documentation:

- Root-level docs:
  - `README.md`          … English (default entry point)
  - `README.ja.md`       … Japanese version of the README
- Optional docs directory:
  - `docs/en/`           … English documents
  - `docs/ja/`           … Japanese documents

When you add new documentation, place:

- English files under the root or `docs/en/`
- Japanese files under `docs/ja/` (or as `*.ja.md` next to the English file)

### File naming conventions

- English:
  - `README.md`
  - `CONTRIBUTING.md`
  - `docs/getting-started.md`
- Japanese:
  - `README.ja.md`
  - `CONTRIBUTING.ja.md`
  - `docs/getting-started.ja.md`

Rules:

- The Japanese version uses the same base filename with a `.ja.md` suffix.
- When both English and Japanese exist, keep their file names aligned (only the `.ja` differs).

### Cross-linking between languages

For documents that have both English and Japanese versions:

- At the top of the **English** document, add a link to the Japanese version, for example:

  `README.md`:
  ```markdown
  [日本語ドキュメントはこちら](./README.ja.md)
  ```

- At the top of the **Japanese** document, add a link back to the English version, for example:

  `README.ja.md`:
  ```markdown
  [English version is here](./README.md)
  ```

Use relative links within the repository.

### Updating documentation

When updating documentation, follow this order:

1. Update the **English** version first.
2. If a Japanese version exists:
   - Update the corresponding `*.ja.md` or `docs/ja/**` file.
   - Keep the content logically aligned with the English original. It does not have to be a word-for-word translation, but it should be consistent in meaning.
3. If you cannot update the Japanese version:
   - Leave a short note in the pull request description (e.g., `Japanese docs not updated yet`).
   - Another contributor may help with translation later.

### Scope of Japanese documentation

- Japanese docs may include:
  - Explanations or notes specific to Japanese users.
  - Additional context for Japan-specific environments, regulations, or services.
- Even in such cases, the English docs remain the source of truth for:
  - Specifications
  - API contracts
  - Formal behavior

If there is any conflict between English and Japanese documentation, the **English** version prevails.
