<script setup>
import { onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    address: {
        type: String,
        default: '',
    },
    height: {
        type: Number,
        default: 280,
    },
});

const mapRoot = ref(null);
const loading = ref(true);
const error = ref('');
const mapsApiKey = String(globalThis?.__YANDEX_MAPS_API_KEY__ ?? '').trim();

let observer = null;
let mapInstance = null;

const ymapsReadyPromise = (() => {
    let promise = null;

    return () => {
        if (promise) {
            return promise;
        }

        promise = new Promise((resolve, reject) => {
            const ensureFreshScriptWithKey = () => {
                const scripts = Array.from(document.querySelectorAll('script[src*="api-maps.yandex.ru/2.1/"]'));
                const expectedPart = `apikey=${encodeURIComponent(mapsApiKey)}`;
                const hasExpected = scripts.some((s) => (s.getAttribute('src') || '').includes(expectedPart));

                if (!hasExpected && scripts.length > 0) {
                    scripts.forEach((s) => s.remove());
                    try {
                        delete window.ymaps;
                    } catch {
                        window.ymaps = undefined;
                    }
                }
            };

            const ready = () => {
                if (!window.ymaps) {
                    reject(new Error('Yandex Maps API unavailable'));
                    return;
                }
                window.ymaps.ready(resolve);
            };

            ensureFreshScriptWithKey();

            if (window.ymaps) {
                ready();
                return;
            }

            const existing = document.querySelector(`script[data-ymaps-script="true"][src*="apikey=${encodeURIComponent(mapsApiKey)}"]`);
            if (existing) {
                existing.addEventListener('load', ready, { once: true });
                existing.addEventListener('error', () => reject(new Error('Failed to load script')), { once: true });
                return;
            }

            const script = document.createElement('script');
            const query = new URLSearchParams({
                lang: 'ru_RU',
                apikey: mapsApiKey,
            });
            script.src = `https://api-maps.yandex.ru/2.1/?${query.toString()}`;
            script.async = true;
            script.defer = true;
            script.dataset.ymapsScript = 'true';
            script.onload = ready;
            script.onerror = () => reject(new Error('Failed to load script'));
            document.head.appendChild(script);
        });

        return promise;
    };
})();

const destroyMap = () => {
    if (mapInstance && typeof mapInstance.destroy === 'function') {
        mapInstance.destroy();
    }
    mapInstance = null;
};

const initMap = async () => {
    if (!props.address || !mapRoot.value) {
        loading.value = false;
        error.value = 'Адрес не указан';
        return;
    }

    try {
        if (!mapsApiKey) {
            throw new Error('Missing yandex maps api key');
        }

        await ymapsReadyPromise();

        const geo = await window.ymaps.geocode(props.address, { results: 1 });
        const first = geo.geoObjects.get(0);

        if (!first) {
            throw new Error('Не удалось найти адрес');
        }

        const coords = first.geometry.getCoordinates();
        const boundedBy = first.properties.get('boundedBy');
        const map = new window.ymaps.Map(mapRoot.value, {
            center: coords,
            zoom: 15,
            controls: ['zoomControl'],
        }, {
            suppressMapOpenBlock: true,
        });

        const marker = new window.ymaps.Placemark(coords, {
            balloonContentHeader: 'Atlantic Group',
            balloonContentBody: first.getAddressLine?.() || props.address,
            hintContent: props.address,
        }, {
            preset: 'islands#redDotIcon',
        });

        map.geoObjects.add(marker);

        if (Array.isArray(boundedBy)) {
            map.setBounds(boundedBy, {
                checkZoomRange: true,
                zoomMargin: 30,
            });
        } else {
            map.setCenter(coords, 15, { duration: 200 });
        }

        map.behaviors.disable('scrollZoom');
        mapInstance = map;
        loading.value = false;
        error.value = '';
    } catch (e) {
        loading.value = false;
        error.value = mapsApiKey ? 'Не удалось загрузить карту' : 'Не указан API ключ Яндекс.Карт';
    }
};

onMounted(() => {
    if (!mapRoot.value) {
        return;
    }

    observer = new IntersectionObserver((entries) => {
        if (!entries[0]?.isIntersecting) {
            return;
        }
        observer?.disconnect();
        observer = null;
        void initMap();
    }, { root: null, rootMargin: '120px', threshold: 0.01 });

    observer.observe(mapRoot.value);
});

onUnmounted(() => {
    observer?.disconnect();
    observer = null;
    destroyMap();
});
</script>

<template>
    <div
        v-if="mapsApiKey"
        ref="mapRoot"
        class="at_map_wrap at_map_wrap--interactive"
        :style="{ height: `${height}px` }"
    >
        <div v-if="loading" class="at_map_placeholder">Загрузка карты…</div>
        <div v-else-if="error" class="at_map_placeholder">{{ error }}</div>
    </div>
</template>
