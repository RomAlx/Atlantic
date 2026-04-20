<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>window.__YANDEX_METRIKA_ID__ = @json($yandexMetrikaCounterId ?? null);</script>
    <script>window.__YANDEX_MAPS_API_KEY__ = @json($yandexMapsApiKey ?? null);</script>
    <title>Atlantic Group</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app"></div>
</body>
</html>
