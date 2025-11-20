<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Docs ファイル詳細</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    <style>
        :root {
            color-scheme: dark;
        }

        .docs-content-markdown {
            line-height: 1.75;
        }

        .docs-content-markdown > *:first-child {
            margin-top: 0;
        }

        .docs-content-markdown h1,
        .docs-content-markdown h2,
        .docs-content-markdown h3,
        .docs-content-markdown h4,
        .docs-content-markdown h5,
        .docs-content-markdown h6 {
            margin-top: 2.5rem;
            margin-bottom: 1.25rem;
            font-weight: 600;
            color: #f8fafc;
        }

        .docs-content-markdown h1 {
            border-bottom: 1px solid rgba(248, 250, 252, 0.08);
            padding-bottom: 0.75rem;
        }

        .docs-content-markdown p {
            margin-bottom: 1rem;
            color: rgba(248, 250, 252, 0.85);
        }

        .docs-content-markdown a {
            color: #7dd3fc;
            text-decoration: underline;
        }

        .docs-content-markdown ul,
        .docs-content-markdown ol {
            padding-left: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .docs-content-markdown blockquote {
            border-left: 4px solid rgba(56, 189, 248, 0.8);
            padding: 0.75rem 1.25rem;
            background-color: rgba(15, 23, 42, 0.8);
            border-radius: 0.75rem;
            margin: 1.75rem 0;
        }

        .docs-content-markdown pre {
            background-color: #020617;
            color: #e2e8f0;
            padding: 1.25rem;
            border-radius: 1rem;
            overflow-x: auto;
            margin: 1.75rem 0;
        }

        .docs-content-markdown code {
            background-color: rgba(56, 189, 248, 0.1);
            border-radius: 0.35rem;
            padding: 0.15rem 0.4rem;
        }

        .docs-content-markdown table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5rem 0;
        }

        .docs-content-markdown table th,
        .docs-content-markdown table td {
            border: 1px solid rgba(248, 250, 252, 0.08);
            padding: 0.75rem;
        }

        .docs-toggle {
            border-radius: 9999px;
            border: 1px solid rgba(248, 250, 252, 0.3);
            padding: 0.4rem 1.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .docs-toggle[data-active="true"] {
            background-color: #f8fafc;
            color: #020617;
            border-color: #f8fafc;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.5);
        }

        .docs-toggle[data-active="false"] {
            background-color: transparent;
            color: rgba(248, 250, 252, 0.75);
        }

        .docs-code-block {
            border: 1px solid rgba(248, 250, 252, 0.08);
            border-radius: 1rem;
            margin: 1.75rem 0;
            overflow: hidden;
            background-color: #020617;
        }

        .docs-code-block__header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1.25rem;
            background-color: rgba(15, 23, 42, 0.8);
            border-bottom: 1px solid rgba(248, 250, 252, 0.05);
        }

        .docs-code-block__header span {
            font-size: 0.85rem;
            color: rgba(248, 250, 252, 0.7);
        }

        .docs-code-block__toggle {
            border: none;
            background: transparent;
            color: #7dd3fc;
            font-weight: 600;
            cursor: pointer;
        }

        .docs-code-block__body {
            max-height: none;
        }

        .docs-code-block.collapsed .docs-code-block__body {
            display: none;
        }

        .docs-copy {
            border-radius: 9999px;
            border: 1px solid rgba(248, 250, 252, 0.2);
            background-color: rgba(248, 250, 252, 0.05);
            color: #f8fafc;
            padding: 0.35rem 0.65rem;
            transition: all 0.15s ease;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .docs-copy:hover {
            border-color: rgba(248, 250, 252, 0.6);
            background-color: rgba(248, 250, 252, 0.12);
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    @php
        $sizeLabel = $sizeBytes < 1024
            ? $sizeBytes.' bytes'
            : number_format($sizeBytes / 1024, 2).' KB';
        $lastUpdated = \Illuminate\Support\Carbon::createFromTimestamp($lastModified)
            ->timezone(config('docs_viewer.timezone') ?? config('app.timezone'))
            ->format('Y-m-d H:i:s');
    @endphp
    <main class="mx-auto w-full max-w-none space-y-8 px-6 py-12">
        <header class="flex flex-col gap-6 rounded-3xl border border-white/10 bg-slate-900/80 p-8 shadow-2xl shadow-black/30 md:flex-row md:items-center md:justify-between">
            <div class="space-y-3">
                <p class="text-xs uppercase tracking-[0.45em] text-slate-400">docs viewer</p>
                <h1 class="text-3xl font-semibold text-white">Docs ファイル詳細</h1>
                <p class="text-sm text-slate-300">選択したファイルのメタ情報と Markdown / Raw 表示を確認できます。</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a
                    href="{{ route($routeNames['viewer']) }}"
                    class="inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white transition hover:border-white/60"
                >
                    一覧に戻る
                </a>
                @if (! empty($links['dashboard']))
                    <a
                        href="{{ $links['dashboard']['url'] }}"
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white transition hover:border-white/60"
                    >
                        {{ $links['dashboard']['label'] }}
                    </a>
                @endif
                @if (! empty($links['home']))
                    <a
                        href="{{ $links['home']['url'] }}"
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white transition hover:border-white/60"
                    >
                        {{ $links['home']['label'] }}
                    </a>
                @endif
            </div>
        </header>

        <section class="rounded-3xl border border-white/5 bg-slate-900/50 p-8 shadow-lg shadow-black/20">
            <h2 class="text-xl font-semibold text-white">ファイル情報</h2>
            <dl class="mt-6 grid gap-6 text-sm text-slate-200 md:grid-cols-2">
                <div class="space-y-1">
                    <dt class="text-xs uppercase tracking-[0.35em] text-slate-400">相対パス</dt>
                    <dd class="flex items-start gap-3 text-base text-white">
                        <span class="font-mono break-all">{{ $relativePath }}</span>
                        <button type="button" class="docs-copy" data-copy-text="{{ $relativePath }}" aria-label="相対パスをコピー">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                        </button>
                    </dd>
                </div>
                <div class="space-y-1">
                    <dt class="text-xs uppercase tracking-[0.35em] text-slate-400">ファイル名</dt>
                    <dd class="flex items-center gap-3 text-lg font-semibold text-white">
                        <span>{{ $fileName }}</span>
                        <button type="button" class="docs-copy" data-copy-text="{{ $fileName }}" aria-label="ファイル名をコピー">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                        </button>
                    </dd>
                </div>
                <div class="space-y-1">
                    <dt class="text-xs uppercase tracking-[0.35em] text-slate-400">サイズ</dt>
                    <dd class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-medium">
                        {{ $sizeLabel }} <span class="text-white/60">({{ number_format($sizeBytes) }} bytes)</span>
                    </dd>
                </div>
                <div class="space-y-1">
                    <dt class="text-xs uppercase tracking-[0.35em] text-slate-400">最終更新</dt>
                    <dd class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-medium">{{ $lastUpdated }}</dd>
                </div>
            </dl>
        </section>

        <section class="overflow-hidden rounded-3xl border border-white/5 bg-slate-900/60 shadow-xl shadow-black/30">
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-white/5 px-6 py-4">
                <p class="text-sm font-semibold uppercase tracking-[0.35em] text-slate-400">コンテンツ</p>
                <div class="flex items-center gap-3" role="group" aria-label="表示モード切替">
                    <div class="flex gap-3">
                        <button type="button" class="docs-toggle" data-docs-content-toggle="markdown" data-active="true">
                            Markdown
                        </button>
                        <button type="button" class="docs-toggle" data-docs-content-toggle="raw" data-active="false">
                            Raw
                        </button>
                    </div>
                    <button
                        type="button"
                        class="docs-copy"
                        data-docs-copy-content="visible"
                        aria-label="表示中のコンテンツをコピー"
                        title="表示中のコンテンツをコピー"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="px-6 py-8 text-base leading-relaxed">
                <div data-docs-content="markdown" class="docs-content-markdown">
                    {!! $markdownHtml !!}
                </div>
                <pre data-docs-content="raw" class="docs-content-raw hidden font-mono text-sm leading-relaxed text-slate-100">{{ $content }}</pre>
            </div>
        </section>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const buttons = document.querySelectorAll('[data-docs-content-toggle]');
            const panels = document.querySelectorAll('[data-docs-content]');
            const copyContentButton = document.querySelector('[data-docs-copy-content="visible"]');
            const collapseThreshold = 10;
            let currentMode = 'markdown';

            const applyMode = (mode) => {
                currentMode = mode;
                panels.forEach((panel) => {
                    const isVisible = panel.getAttribute('data-docs-content') === mode;
                    panel.classList.toggle('hidden', !isVisible);
                });

                buttons.forEach((button) => {
                    const isActive = button.getAttribute('data-docs-content-toggle') === mode;
                    button.setAttribute('data-active', isActive ? 'true' : 'false');
                });
            };

            buttons.forEach((button) => {
                button.addEventListener('click', () => {
                    applyMode(button.getAttribute('data-docs-content-toggle'));
                });
            });

            applyMode('markdown');

            const wrapCodeBlocks = () => {
                const markdownContainer = document.querySelector('.docs-content-markdown');
                if (!markdownContainer) {
                    return;
                }

                const codeBlocks = markdownContainer.querySelectorAll('pre');
                codeBlocks.forEach((pre, index) => {
                    if (pre.closest('.docs-code-block')) {
                        return;
                    }

                    const lineCount = (pre.textContent || '').split('\n').length;
                    const wrapper = document.createElement('div');
                    wrapper.className = 'docs-code-block';

                    const header = document.createElement('div');
                    header.className = 'docs-code-block__header';

                    const title = document.createElement('span');
                    title.textContent = `コードブロック #${index + 1}（${lineCount}行）`;

                    const toggleButton = document.createElement('button');
                    toggleButton.type = 'button';
                    toggleButton.className = 'docs-code-block__toggle';
                    toggleButton.textContent = lineCount >= collapseThreshold ? '展開' : '折りたたむ';

                    const body = document.createElement('div');
                    body.className = 'docs-code-block__body';

                    const originalPre = pre.cloneNode(true);
                    body.appendChild(originalPre);

                    header.appendChild(title);
                    header.appendChild(toggleButton);
                    wrapper.appendChild(header);
                    wrapper.appendChild(body);
                    pre.replaceWith(wrapper);

                    const setCollapsed = (shouldCollapse) => {
                        wrapper.classList.toggle('collapsed', shouldCollapse);
                        toggleButton.textContent = shouldCollapse ? '展開' : '折りたたむ';
                    };

                    setCollapsed(lineCount >= collapseThreshold);

                    toggleButton.addEventListener('click', () => {
                        const isCollapsed = wrapper.classList.contains('collapsed');
                        setCollapsed(!isCollapsed);
                    });
                });
            };

            wrapCodeBlocks();

            const copyButtons = document.querySelectorAll('[data-copy-text]');
            const fallbackCopy = (text) => {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.left = '-9999px';
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
            };

            const setCopied = (button) => {
                const originalHtml = button.getAttribute('data-original-html') ?? button.innerHTML;
                button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                button.disabled = true;
                setTimeout(() => {
                    button.innerHTML = originalHtml;
                    button.disabled = false;
                }, 1500);
            };

            copyButtons.forEach((button) => {
                if (!button.getAttribute('data-original-html')) {
                    button.setAttribute('data-original-html', button.innerHTML);
                }

                button.addEventListener('click', async () => {
                    const text = button.getAttribute('data-copy-text') ?? '';

                    try {
                        if (navigator?.clipboard?.writeText) {
                            await navigator.clipboard.writeText(text);
                        } else {
                            fallbackCopy(text);
                        }
                        setCopied(button);
                    } catch (error) {
                        console.error('Clipboard copy failed', error);
                    }
                });
            });

            const copyVisibleContent = () => {
                const target = currentMode === 'raw'
                    ? document.querySelector('[data-docs-content="raw"]')
                    : document.querySelector('[data-docs-content="markdown"]');

                if (!target) {
                    return;
                }

                const text = target.textContent ?? '';

                const copyAction = async () => {
                    if (navigator?.clipboard?.writeText) {
                        await navigator.clipboard.writeText(text);
                    } else {
                        fallbackCopy(text);
                    }
                };

                copyAction()
                    .then(() => {
                        if (copyContentButton instanceof HTMLButtonElement) {
                            if (!copyContentButton.getAttribute('data-original-html')) {
                                copyContentButton.setAttribute('data-original-html', copyContentButton.innerHTML);
                            }
                            setCopied(copyContentButton);
                        }
                    })
                    .catch((error) => console.error('Clipboard copy failed', error));
            };

            if (copyContentButton instanceof HTMLButtonElement) {
                if (!copyContentButton.getAttribute('data-original-html')) {
                    copyContentButton.setAttribute('data-original-html', copyContentButton.innerHTML);
                }
                copyContentButton.addEventListener('click', copyVisibleContent);
            }
        });
    </script>
</body>
</html>
