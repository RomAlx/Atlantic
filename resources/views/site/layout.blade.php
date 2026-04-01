<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $seoTitle ?? $title }} | Atlantic Group</title>
    <meta name="description" content="{{ $seoDescription ?? 'Atlantic Group' }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="{{ $seoTitle ?? $title }}">
    <meta property="og:description" content="{{ $seoDescription ?? 'Atlantic Group' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta name="robots" content="index,follow">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">
    <header class="border-b bg-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-4 py-4">
            <a href="{{ route('site.home') }}" class="text-xl font-bold">Atlantic Group</a>
            <form action="{{ route('site.search') }}" method="get" class="flex w-full max-w-md gap-2">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Поиск товаров" class="w-full rounded border px-3 py-2">
                <button class="rounded bg-blue-600 px-4 py-2 text-white">Найти</button>
            </form>
            <nav class="flex flex-wrap gap-4 text-sm">
                <a href="{{ route('site.about') }}" class="hover:underline">О компании</a>
                <a href="{{ route('site.catalog') }}" class="hover:underline">Каталог</a>
                <a href="{{ route('site.support') }}" class="hover:underline">Техподдержка</a>
                <a href="{{ route('site.contacts') }}" class="hover:underline">Контакты</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8">
        <h1 class="mb-6 text-3xl font-semibold">{{ $title }}</h1>
        @if (session('feedback_success'))
            <div class="mb-6 rounded border border-emerald-300 bg-emerald-50 px-4 py-3 text-emerald-800">
                {{ session('feedback_success') }}
            </div>
        @endif
        @yield('content')
    </main>

    <footer class="mt-8 border-t bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-6 text-sm text-slate-600">
            <span>{{ now()->year }} Atlantic Group</span>
            <div class="flex gap-4">
                <a href="{{ route('site.sitemap') }}" class="hover:underline">sitemap.xml</a>
                <a href="{{ route('site.robots') }}" class="hover:underline">robots.txt</a>
            </div>
        </div>
    </footer>
</body>
</html>
