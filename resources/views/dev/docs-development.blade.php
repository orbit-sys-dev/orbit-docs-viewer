<!DOCTYPE html>
<html lang="{{ $lang ?? 'ja' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $t['development_page']['title'] ?? 'Development Docs' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
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
                        <p class="text-xs uppercase tracking-[0.45em] text-slate-400">documentation</p>
                        <h1 class="mt-3 text-3xl font-semibold">{{ $t['development_page']['heading'] ?? 'Development docs' }}</h1>
                    </div>
                    <p class="text-sm leading-relaxed text-slate-300">
                        {{ $t['development_page']['description'] ?? '' }}
                    </p>
                </div>
                <div class="flex flex-col gap-4 text-sm text-slate-300">
                    <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-center font-semibold">
                        {{ trans('docs-viewer::messages.development_page.total', ['count' => $totalCount], $locale) }}
                    </div>
                    <a
                        href="{{ route($routeNames['viewer'], $langQuery) }}"
                        class="inline-flex items-center justify-center rounded-full border border-white/20 px-5 py-2 text-sm font-semibold text-white transition hover:border-white/60"
                    >
                        {{ $t['nav']['back_to_viewer'] ?? 'Back to Docs Viewer' }}
                    </a>
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

        @php
            $memoCard = $subGroups['memo'] ?? null;
            $otherGroups = collect($subGroups)->reject(fn ($_, $key) => $key === 'memo');
        @endphp

        @if ($memoCard)
            <div class="flex flex-col rounded-2xl border border-white/5 bg-slate-900/60 backdrop-blur shadow-lg shadow-black/20">
                <div class="flex items-center justify-between border-b border-white/5 px-5 py-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-white/60">{{ $t['development_page']['badge'] ?? 'Development' }}</p>
                        <p class="text-xl font-semibold text-white">{{ $memoCard['label'] }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-white/60">{{ $t['development_page']['total_label'] ?? 'Total' }}</p>
                        <p class="text-2xl font-semibold text-white">{{ count($memoCard['files']) }}</p>
                    </div>
                </div>
                <div class="flex-1 space-y-3 px-5 py-4">
                    @php
                        $preview = $memoCard['preview']['files'] ?? $memoCard['files'];
                    @endphp
                    @if (empty($preview))
                        <p class="rounded-xl border border-dashed border-white/10 px-3 py-4 text-center text-sm text-white/50">
                            {{ $t['development_page']['no_files'] ?? 'No files found' }}
                        </p>
                    @else
                            <ul class="space-y-2">
                                @foreach ($preview as $file)
                                    <li>
                                        <a
                                            href="{{ route($routeNames['viewer.file'], array_merge($langQuery, ['path' => $file])) }}"
                                            class="flex items-center gap-3 rounded-2xl border border-white/10 px-3 py-2 text-sm text-white transition hover:border-white/40 hover:bg-white/5"
                                        >
                                        <span class="rounded-full bg-white/10 px-2 py-0.5 text-xs text-white/70">{{ $t['development_page']['detail'] ?? 'Detail' }}</span>
                                        <span class="font-mono text-xs text-white/80 break-all">{{ $file }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="border-t border-white/5 px-5 py-4">
                    <a
                        href="{{ $memoCard['route'] }}"
                        class="flex items-center justify-center rounded-full border border-white/20 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-white/80 transition hover:border-white/60"
                    >
                        {{ $t['development_page']['open_list'] ?? 'View list' }}
                    </a>
                </div>
            </div>
        @endif

        <div class="grid gap-6 md:grid-cols-2">
            @foreach ($otherGroups as $subGroup)
                <div class="flex flex-col rounded-2xl border border-white/5 bg-slate-900/60 backdrop-blur shadow-lg shadow-black/20">
                    <div class="flex items-center justify-between border-b border-white/5 px-5 py-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.3em] text-white/60">{{ $t['development_page']['badge'] ?? 'Development' }}</p>
                            <p class="text-xl font-semibold text-white">{{ $subGroup['label'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-white/60">{{ $t['development_page']['total_label'] ?? 'Total' }}</p>
                            <p class="text-2xl font-semibold text-white">{{ count($subGroup['files']) }}</p>
                        </div>
                    </div>
                    <div class="flex-1 space-y-3 px-5 py-4">
                        @php
                            $preview = $subGroup['preview']['files'] ?? $subGroup['files'];
                        @endphp
                        @if (empty($preview))
                            <p class="rounded-xl border border-dashed border-white/10 px-3 py-4 text-center text-sm text-white/50">
                                {{ $t['development_page']['no_files'] ?? 'No files found' }}
                            </p>
                        @else
                            <ul class="space-y-2">
                                @foreach ($preview as $file)
                                    <li>
                                        <a
                                            href="{{ route($routeNames['viewer.file'], array_merge($langQuery, ['path' => $file])) }}"
                                            class="flex items-center gap-3 rounded-2xl border border-white/10 px-3 py-2 text-sm text-white transition hover:border-white/40 hover:bg-white/5"
                                        >
                                            <span class="rounded-full bg-white/10 px-2 py-0.5 text-xs text-white/70">{{ $t['development_page']['detail'] ?? 'Detail' }}</span>
                                            <span class="font-mono text-xs text-white/80 break-all">{{ $file }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div class="border-t border-white/5 px-5 py-4">
                        <a
                            href="{{ $subGroup['route'] }}"
                            class="flex items-center justify-center rounded-full border border-white/20 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-white/80 transition hover:border-white/60"
                        >
                            {{ $t['development_page']['open_list'] ?? 'View list' }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </main>
</body>
</html>
