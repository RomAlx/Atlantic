<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php($ymId = ($yandexMetrikaEnabled ?? false) && trim((string) ($yandexMetrikaCounterId ?? '')) !== '' ? trim((string) $yandexMetrikaCounterId) : null)
    <script>window.__YANDEX_METRIKA_ID__ = @json($ymId);</script>
    <title>Atlantic Group</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app"></div>
    @if($yandexMetrikaEnabled && $yandexMetrikaCounterId !== '')
        @php($ymId = json_encode($yandexMetrikaCounterId, JSON_THROW_ON_ERROR))
        <script>
            (function (m, e, t, r, i, k, a) {
                m[i] = m[i] || function () {
                    (m[i].a = m[i].a || []).push(arguments)
                };
                m[i].l = 1 * new Date();
                for (var j = 0; j < document.scripts.length; j++) {
                    if (document.scripts[j].src === r) {
                        return;
                    }
                }
                k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
            })(window, document, 'script', 'https://mc.yandex.ru/metrika/tag.js', 'ym');
            ym({!! $ymId !!}, 'init', {
                defer: true,
                ssr: true,
                webvisor: true,
                clickmap: true,
                accurateTrackBounce: true,
                trackLinks: true
            });
        </script>
        <noscript>
            <div>
                <img src="https://mc.yandex.ru/watch/{{ $yandexMetrikaCounterId }}" style="position:absolute; left:-9999px;" alt="">
            </div>
        </noscript>
    @endif
</body>
</html>
