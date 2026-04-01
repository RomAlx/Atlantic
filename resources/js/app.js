import './bootstrap';
import 'toastify-js/src/toastify.css';
import { createApp, nextTick } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';

router.afterEach((to) => {
    const id = window.__YANDEX_METRIKA_ID__;
    if (id == null || id === '' || typeof window.ym !== 'function') {
        return;
    }
    nextTick(() => {
        const loc = `${window.location.origin}${to.fullPath}`;
        window.ym(id, 'hit', loc, { title: document.title });
    });
});

createApp(App)
    .use(createPinia())
    .use(router)
    .mount('#app');
