<!DOCTYPE html>
<html lang="{{ $lang ?? 'ja' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trans('docs-viewer::messages.development_group.page_title', ['group' => $groupLabel], $lang ?? 'ja') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    @php
        $locale = $lang ?? 'ja';
    @endphp
    <main class="mx-auto w-full max-w-none px-6 py-12">
        <section class="flex flex-col gap-6 rounded-3xl border border-white/10 bg-slate-900/80 p-8 shadow-2xl shadow-black/40 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.45em] text-slate-400">development</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">{{ trans('docs-viewer::messages.development_group.heading', ['group' => $groupLabel], $locale) }}</h1>
                <p class="mt-3 text-sm text-slate-300">
                    {!! trans(
                        'docs-viewer::messages.development_group.description',
                        ['path' => '<code class="rounded bg-white/10 px-2 py-1 text-xs text-white">'.$rootPrefix.'development/'.$groupKey.'/</code>'],
                        $locale
                    ) !!}
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a
                    class="inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white transition hover:border-white/60"
                    href="{{ route($routeNames['viewer'], $langQuery) }}"
                >
                    {{ $t['nav']['back_to_viewer'] ?? 'Back to Docs Viewer' }}
                </a>
                <a
                    class="inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white transition hover:border-white/60"
                    href="{{ route($routeNames['development'], $langQuery) }}"
                >
                    {{ $t['nav']['back_to_development'] ?? 'Back to Development cards' }}
                </a>
                @if (! empty($links['dashboard']))
                    <a
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white transition hover:border-white/60"
                        href="{{ $links['dashboard']['url'] }}"
                    >
                        {{ $links['dashboard']['label'] }}
                    </a>
                @endif
                @if (! empty($links['home']))
                    <a
                        class="inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white transition hover:border-white/60"
                        href="{{ $links['home']['url'] }}"
                    >
                        {{ $links['home']['label'] }}
                    </a>
                @endif
            </div>
        </section>

        <section class="mt-8 overflow-hidden rounded-3xl border border-white/5 bg-slate-900/60 shadow-xl shadow-black/30">
            <div class="flex items-center justify-between border-b border-white/5 px-6 py-4">
                <p class="text-sm font-semibold uppercase tracking-[0.35em] text-white/60">{{ trans('docs-viewer::messages.development_group.list_title', locale: $locale) }}</p>
                <span class="inline-flex items-center gap-2 rounded-full border border-white/15 px-4 py-1 text-sm text-white/80">
                    <span class="text-xs uppercase tracking-[0.35em]">{{ trans('docs-viewer::messages.development_group.total_label', locale: $locale) }}</span>
                    <span class="text-2xl text-white">{{ count($files) }}</span>
                </span>
            </div>
            @if (empty($files))
                <p class="px-6 py-10 text-center text-sm text-white/60">{{ trans('docs-viewer::messages.development_group.empty', locale: $locale) }}</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10 text-sm">
                        <thead class="bg-white/5 text-xs uppercase tracking-[0.35em] text-white/60">
                            <tr>
                                <th class="px-4 py-3 text-left">{{ trans('docs-viewer::messages.development_group.table.action', locale: $locale) }}</th>
                                <th class="px-4 py-3 text-left">{{ trans('docs-viewer::messages.development_group.table.path', locale: $locale) }}</th>
                                <th class="px-4 py-3 text-left">{{ trans('docs-viewer::messages.development_group.table.name', locale: $locale) }}</th>
                                <th class="px-4 py-3 text-left">{{ trans('docs-viewer::messages.development_group.table.updated_at', locale: $locale) }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach ($files as $file)
                                <tr class="hover:bg-white/5">
                                    <td class="px-4 py-3">
                                        <a
                                            class="inline-flex items-center rounded-full border border-white/20 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-white/80 transition hover:border-white/60"
                                            href="{{ route($routeNames['viewer.file'], array_merge($langQuery, ['path' => $file['path']])) }}"
                                        >
                                            {{ $t['nav']['open'] ?? 'Open' }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-white/90 break-all">{{ $file['path'] }}</td>
                                    <td class="px-4 py-3 text-white">{{ $file['name'] }}</td>
                                    <td class="px-4 py-3 text-white/60">{{ $file['updated_at'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </main>
</body>
</html>
