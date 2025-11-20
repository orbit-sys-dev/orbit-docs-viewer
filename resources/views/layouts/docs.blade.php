<!DOCTYPE html>
<html lang="{{ $lang ?? config('app.locale', 'ja') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Docs Viewer')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <main class="mx-auto w-full max-w-7xl px-6 py-10">
        @yield('content')
    </main>
</body>
</html>
