<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Development {{ $groupLabel }} 一覧</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <main class="mx-auto w-full max-w-none px-6 py-12">
        <section class="flex flex-col gap-6 rounded-3xl border border-white/10 bg-slate-900/80 p-8 shadow-2xl shadow-black/40 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.45em] text-slate-400">development</p>
                <h1 class="mt-3 text-3xl font-semibold text-white">Development / {{ $groupLabel }}</h1>
                <p class="mt-3 text-sm text-slate-300">
                    <code class="rounded bg-white/10 px-2 py-1 text-xs text-white">{{ $rootPrefix }}development/{{ $groupKey }}/</code>
                    配下のドキュメントを一覧表示しています。
                </p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a
                    class="inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white transition hover:border-white/60"
                    href="{{ route($routeNames['viewer']) }}"
                >
                    Docs ビューアーに戻る
                </a>
                <a
                    class="inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white transition hover:border-white/60"
                    href="{{ route($routeNames['development']) }}"
                >
                    Development カード一覧
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
                <p class="text-sm font-semibold uppercase tracking-[0.35em] text-white/60">ドキュメント一覧</p>
                <span class="inline-flex items-center gap-2 rounded-full border border-white/15 px-4 py-1 text-sm text-white/80">
                    <span class="text-xs uppercase tracking-[0.35em]">Total</span>
                    <span class="text-2xl text-white">{{ count($files) }}</span>
                </span>
            </div>
            @if (empty($files))
                <p class="px-6 py-10 text-center text-sm text-white/60">現在表示できるドキュメントはありません。</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10 text-sm">
                        <thead class="bg-white/5 text-xs uppercase tracking-[0.35em] text-white/60">
                            <tr>
                                <th class="px-4 py-3 text-left">操作</th>
                                <th class="px-4 py-3 text-left">パス</th>
                                <th class="px-4 py-3 text-left">ファイル名</th>
                                <th class="px-4 py-3 text-left">最終更新</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach ($files as $file)
                                <tr class="hover:bg-white/5">
                                    <td class="px-4 py-3">
                                        <a
                                            class="inline-flex items-center rounded-full border border-white/20 px-3 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-white/80 transition hover:border-white/60"
                                            href="{{ route($routeNames['viewer.file'], ['path' => $file['path']]) }}"
                                        >
                                            開く
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
