<!DOCTYPE html>
<html lang="{{ $lang ?? 'ja' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $t['viewer']['page_title'] ?? 'Docs Viewer' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    <style>
        details.docs-card > summary::-webkit-details-marker {
            display: none;
        }

        details.docs-card > summary {
            cursor: pointer;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    @php
        $locale = $lang ?? 'ja';
    @endphp
    <main class="mx-auto flex w-full max-w-none flex-col gap-8 px-6 py-12">
        <section class="rounded-3xl border border-white/10 bg-slate-900/80 p-8 shadow-2xl shadow-black/40">
            <div class="flex flex-col gap-8 md:flex-row md:items-center md:justify-between">
                <div class="space-y-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.45em] text-slate-400">{{ $t['viewer']['documentation'] ?? 'documentation' }}</p>
                        <h1 class="mt-3 text-3xl font-semibold">{{ $t['viewer']['page_title'] ?? 'Docs Viewer' }}</h1>
                    </div>
                    <p class="text-sm leading-relaxed text-slate-300">
                        <code class="rounded bg-white/10 px-2 py-1 text-xs text-white">{{ $rootPrefix }}</code>
                        {{ trans('docs-viewer::messages.viewer.description', ['prefix' => $rootPrefix], $locale) }}
                        <a class="text-sky-300 underline-offset-4 hover:underline" href="{{ route($routeNames['viewer.file'], array_merge($langQuery, ['path' => $rootPrefix.'dashboards/dev_docs_viewer.md'])) }}">{{ $t['viewer']['spec_link'] ?? 'Spec' }}</a>
                    </p>
                    <a href="{{ route($routeNames['viewer.file'], array_merge($langQuery, ['path' => $readmePath])) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white hover:text-sky-200">
                        {{ $t['viewer']['readme_link'] ?? 'Open repository README' }}
                        <span aria-hidden="true" class="text-sky-300">→</span>
                    </a>
                </div>
                <div class="flex flex-col gap-4 text-sm text-slate-300">
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-center font-semibold">
                        {{ trans('docs-viewer::messages.viewer.total', ['count' => $totalCount], $locale) }}
                    </div>
                    @if (! empty($links['dashboard']))
                        <a
                            href="{{ $links['dashboard']['url'] }}"
                            class="inline-flex items-center justify-center rounded-full border border-white/20 px-5 py-2 text-sm font-semibold text-white transition hover:border-white/60"
                        >
                            {{ $links['dashboard']['label'] }}
                        </a>
                    @endif
                    @if (! empty($links['home']))
                        <a
                            href="{{ $links['home']['url'] }}"
                            class="inline-flex items-center justify-center rounded-full border border-white/20 px-5 py-2 text-sm font-semibold text-white transition hover:border-white/60"
                        >
                            {{ $links['home']['label'] }}
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <div class="grid gap-4 md:grid-cols-2">
            @if (! empty($appGroup))
                @php
                    $appCount = count($appGroup['files']);
                @endphp
                <section class="flex flex-col gap-4 rounded-3xl border border-dashed border-white/15 bg-slate-900/60 p-6 shadow-inner shadow-black/30">
                    <div class="space-y-2">
                        <p class="text-xs uppercase tracking-[0.45em] text-slate-400">{{ $t['viewer']['app']['title'] ?? 'App docs' }}</p>
                        <h2 class="text-2xl font-semibold text-white">{{ $t['viewer']['app']['title'] ?? 'App docs' }}</h2>
                        <p class="text-sm text-slate-300">{{ $t['viewer']['app']['description'] ?? '' }}</p>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-center text-sm font-semibold text-white">
                            {{ trans('docs-viewer::messages.viewer.total', ['count' => $appCount], $locale) }}
                        </div>
                        <a
                            href="{{ route($routeNames['app'], $langQuery) }}"
                            class="inline-flex items-center justify-center rounded-full border border-white/20 px-5 py-2 text-sm font-semibold text-white transition hover:border-white/60"
                        >
                            {{ $t['viewer']['app']['cta'] ?? 'Open App Docs' }}
                        </a>
                    </div>
                </section>
            @endif
            @if (! empty($developmentGroup))
                @php
                    $developmentCount = count($developmentGroup['files']);
                @endphp
                <section class="flex flex-col gap-4 rounded-3xl border border-dashed border-white/15 bg-slate-900/60 p-6 shadow-inner shadow-black/30">
                    <div class="space-y-2">
                        <p class="text-xs uppercase tracking-[0.45em] text-slate-400">development</p>
                        <h2 class="text-2xl font-semibold text-white">{{ $t['viewer']['development']['title'] ?? 'Development' }}</h2>
                        <p class="text-sm text-slate-300">{{ $t['viewer']['development']['description'] ?? '' }}</p>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-center text-sm font-semibold text-white">
                            {{ trans('docs-viewer::messages.viewer.total', ['count' => $developmentCount], $locale) }}
                        </div>
                        <a
                            href="{{ route($routeNames['development'], $langQuery) }}"
                            class="inline-flex items-center justify-center rounded-full border border-white/20 px-5 py-2 text-sm font-semibold text-white transition hover:border-white/60"
                        >
                            {{ $t['viewer']['development']['cta'] ?? 'Open Development' }}
                        </a>
                    </div>
                </section>
            @endif
        </div>


        <div class="flex flex-col gap-6">
            @foreach ($groups as $category => $group)
                @php
                    $count = count($group['files']);
                    $forceOpen = in_array($category, ['dashboards', 'development', 'app'], true);
                    $shouldCollapse = $count >= 10 && ! $forceOpen;
                    $subGroups = $group['groups'];
                    $hasSubGroups = ! empty($subGroups);
                    $isAppCategory = $category === 'app';
                @endphp
                <details
                    class="docs-card overflow-hidden rounded-3xl border border-white/5 bg-slate-900/60 backdrop-blur shadow-lg shadow-black/20 transition hover:border-white/15"
                    data-docs-category="{{ $category }}"
                    @if (! $shouldCollapse || $forceOpen) open @endif
                >
                    <summary class="flex items-center justify-between gap-4 px-6 py-5 text-lg font-semibold text-white/90">
                        <span>{{ $group['label'] }}</span>
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-1 text-sm text-white/80">
                            <span class="text-xs uppercase tracking-[0.3em]">{{ $t['viewer']['cards']['files_label'] ?? 'Files' }}</span>
                            <span class="text-xl text-white">{{ $count }}</span>
                        </span>
                    </summary>
                    <div class="border-t border-white/5 px-6 py-6">
                        @if ($hasSubGroups)
                            <div class="{{ $isAppCategory ? 'flex flex-col gap-4' : 'grid gap-4 md:grid-cols-2 xl:grid-cols-3' }}">
                                @foreach ($subGroups as $subKey => $subGroup)
                                    <div class="flex flex-col rounded-2xl border border-white/5 bg-slate-900/40 p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm uppercase tracking-[0.3em] text-white/50">{{ $group['label'] }}</p>
                                                <p class="text-lg font-semibold text-white">{{ $subGroup['label'] }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs text-white/60">Total</p>
                                                <p class="text-xl font-semibold text-white">{{ count($subGroup['files']) }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-4 flex-1 space-y-2">
                                        @if (empty($subGroup['files']))
                                            <p class="rounded-xl border border-dashed border-white/10 px-3 py-4 text-center text-sm text-white/50">
                                                {{ $t['viewer']['cards']['no_files'] ?? 'No files found' }}
                                            </p>
                                        @else
                                                @php
                                                    $preview = $subGroup['preview']['files'] ?? $subGroup['files'];
                                                    $extraCount = $subGroup['preview']['extra_count'] ?? 0;
                                                @endphp
                                                <ul class="space-y-2">
                                                    @foreach ($preview as $file)
                                                        <li>
                                                            <a
                                                                href="{{ route($routeNames['viewer.file'], array_merge($langQuery, ['path' => $file])) }}"
                                                                class="flex items-center gap-3 rounded-2xl border border-white/10 px-3 py-2 text-sm text-white transition hover:border-white/40 hover:bg-white/5"
                                                            >
                                                                <span class="rounded-full bg-white/10 px-2 py-0.5 text-xs text-white/70">{{ $t['viewer']['cards']['detail'] ?? 'Detail' }}</span>
                                                                <span class="font-mono text-xs text-white/80 break-all">{{ $file }}</span>
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                @if ($extraCount > 0)
                                                    <p class="text-xs text-white/60">
                                                        {{ trans('docs-viewer::messages.viewer.cards.extra_notice', ['count' => $extraCount], $locale) }}
                                                    </p>
                                                @endif
                                            @endif
                                        </div>
                                        @if (! empty($subGroup['route']))
                                            <a
                                                href="{{ $subGroup['route'] }}"
                                                class="mt-4 inline-flex items-center justify-center rounded-full border border-white/20 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-white/80 transition hover:border-white/60"
                                            >
                                                {{ $t['viewer']['cards']['show_list'] ?? 'View list' }}
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            @if (empty($group['files']))
                                <p class="rounded-2xl border border-dashed border-white/10 px-6 py-10 text-center text-sm text-white/60">
                                    {{ trans('docs-viewer::messages.viewer.cards.listing_empty', ['path' => "docs/{$category}/"], $locale) }}
                                </p>
                                        @else
                                            <ul class="space-y-2">
                                                @foreach ($group['files'] as $file)
                                                    <li>
                                                        <a
                                                            href="{{ route($routeNames['viewer.file'], array_merge($langQuery, ['path' => $file])) }}"
                                                            class="flex items-center gap-4 rounded-2xl border border-white/10 px-4 py-3 text-sm text-white transition hover:border-white/40 hover:bg-white/5"
                                                        >
                                                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs uppercase tracking-[0.3em] text-white/70">{{ $t['viewer']['cards']['detail'] ?? 'Detail' }}</span>
                                                            <span class="font-mono text-xs text-white/90 break-all">{{ $file }}</span>
                                                        </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        @endif
                    </div>
                </details>
            @endforeach
        </div>
    </main>
</body>
</html>
