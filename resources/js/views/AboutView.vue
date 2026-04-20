<script setup>
import { computed, onMounted, ref } from 'vue';
import { fetchJson } from '../services/api';
import SiteBreadcrumbs from '../components/SiteBreadcrumbs.vue';

const loading = ref(true);
const data = ref({ item: null });

const normalizedVideoUrl = computed(() => {
    const raw = data.value.item?.video_url;
    if (!raw || typeof raw !== 'string') {
        return null;
    }

    const value = raw.trim();
    if (value === '') {
        return null;
    }

    if (/^https?:\/\//i.test(value)) {
        return value;
    }

    return `https://${value}`;
});

const videoEmbedUrl = computed(() => {
    const value = normalizedVideoUrl.value;
    if (!value) {
        return null;
    }

    try {
        const url = new URL(value);
        const host = url.hostname.toLowerCase();

        if (host.includes('youtube.com')) {
            const id = url.searchParams.get('v');
            if (id) {
                return `https://www.youtube.com/embed/${id}`;
            }

            const parts = url.pathname.split('/').filter(Boolean);
            const marker = parts.findIndex((part) => ['shorts', 'embed', 'live'].includes(part));
            if (marker !== -1 && parts[marker + 1]) {
                return `https://www.youtube.com/embed/${parts[marker + 1]}`;
            }

            return value;
        }

        if (host.includes('youtu.be')) {
            const id = url.pathname.split('/').filter(Boolean)[0];
            return id ? `https://www.youtube.com/embed/${id}` : null;
        }

        if (host.includes('rutube.ru')) {
            const parts = url.pathname.split('/').filter(Boolean);

            if (parts[0] === 'play' && parts[1] === 'embed' && parts[2]) {
                return `https://rutube.ru/play/embed/${parts[2]}`;
            }

            if (parts[0] === 'video' && parts[1]) {
                return `https://rutube.ru/play/embed/${parts[1]}`;
            }

            return null;
        }

        return null;
    } catch {
        return null;
    }
});

const isDirectVideo = computed(() => {
    const value = normalizedVideoUrl.value;
    if (!value) {
        return false;
    }

    return /\.(mp4|webm|ogg)(\?.*)?$/i.test(value);
});

onMounted(async () => {
    loading.value = true;

    try {
        data.value = await fetchJson('/api/pages/about');
        document.title = `${data.value.item?.seo_title || data.value.item?.title || 'О компании'} | Atlantic Group`;
    } catch (e) {
        data.value = { item: { title: 'О компании', content_html: '<p>Информация о компании скоро будет опубликована.</p>' } };
        document.title = 'О компании | Atlantic Group';
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <section class="at_support_article_wrap">
        <SiteBreadcrumbs :items="[{ label: 'Главная', to: '/' }, { label: data.item?.title || 'О компании' }]" />

        <p v-if="loading" class="text-center">Загрузка...</p>

        <article v-else-if="data.item" class="at_support_article">
            <h1 class="h1">
                <span class="underline_bottom">{{ data.item.title }}</span>
            </h1>

            <div class="prose max-w-none at_md_prose" v-html="data.item.content_html || '<p>Контент пока не заполнен.</p>'" />

            <div v-if="videoEmbedUrl && !isDirectVideo" class="at_support_video mt-4">
                <iframe
                    :src="videoEmbedUrl"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen
                    loading="lazy"
                />
            </div>
            <div v-else-if="isDirectVideo && normalizedVideoUrl" class="at_support_video mt-4">
                <video controls :src="normalizedVideoUrl" class="w-100" />
            </div>
        </article>
    </section>
</template>
