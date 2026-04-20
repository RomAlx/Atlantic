import './bootstrap';
import 'toastify-js/src/toastify.css';
import { createApp, nextTick } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';

const YANDEX_METRIKA_ID = Number(window.__YANDEX_METRIKA_ID__ ?? 0);

router.afterEach((to) => {
    const visitPayload = {
        path: to.fullPath,
        route_name: String(to.name ?? ''),
        product_slug: typeof to.params?.productSlug === 'string' ? to.params.productSlug : '',
        support_article_slug: typeof to.params?.slug === 'string' && to.name === 'support-article' ? to.params.slug : '',
    };

    fetch('/api/visits/hit', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(visitPayload),
        keepalive: true,
    }).catch(() => {});

    if (!Number.isFinite(YANDEX_METRIKA_ID) || YANDEX_METRIKA_ID <= 0 || typeof window.ym !== 'function') {
        return;
    }
    nextTick(() => {
        const loc = `${window.location.origin}${to.fullPath}`;
        window.ym(YANDEX_METRIKA_ID, 'hit', loc, { title: document.title });
    });
});

createApp(App)
    .use(createPinia())
    .use(router)
    .mount('#app');
