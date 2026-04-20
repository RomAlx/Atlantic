<script setup>
import { onMounted } from 'vue';

const counterId = Number(globalThis?.__YANDEX_METRIKA_ID__ ?? 0);
const hasCounterId = Number.isFinite(counterId) && counterId > 0;

onMounted(() => {
    if (!hasCounterId) {
        return;
    }

    (function (m, e, t, r, i, k, a) {
        m[i] = m[i] || function () {
            (m[i].a = m[i].a || []).push(arguments);
        };
        m[i].l = 1 * new Date();
        for (let j = 0; j < document.scripts.length; j += 1) {
            if (document.scripts[j].src === r) {
                return;
            }
        }
        k = e.createElement(t);
        a = e.getElementsByTagName(t)[0];
        k.async = 1;
        k.src = r;
        a.parentNode.insertBefore(k, a);
    })(window, document, 'script', `https://mc.yandex.ru/metrika/tag.js?id=${counterId}`, 'ym');

    window.ym(counterId, 'init', {
        ssr: true,
        webvisor: true,
        clickmap: true,
        ecommerce: 'dataLayer',
        referrer: document.referrer,
        url: location.href,
        accurateTrackBounce: true,
        trackLinks: true,
    });
});
</script>

<template>
    <noscript v-if="hasCounterId">
        <div>
            <img
                :src="`https://mc.yandex.ru/watch/${counterId}`"
                style="position:absolute; left:-9999px;"
                alt=""
            >
        </div>
    </noscript>
</template>
