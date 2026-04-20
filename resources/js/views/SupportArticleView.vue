<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { fetchJson } from '../services/api';

const route = useRoute();
const loading = ref(true);
const error = ref('');
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

const load = async () => {
    loading.value = true;
    error.value = '';

    try {
        data.value = await fetchJson(`/api/support/${route.params.slug}`);
        document.title = `${data.value.item?.seo_title || data.value.item?.title || 'Статья'} | Atlantic Group`;
    } catch (e) {
        error.value = 'Ошибка загрузки статьи';
    } finally {
        loading.value = false;
    }
};

onMounted(load);
watch(() => route.params.slug, load);
</script>

<template>
    <section class="at_support_article_wrap">
        <p class="mb-3">
            <RouterLink to="/support" class="text-decoration-none">&larr; Назад к статьям</RouterLink>
        </p>

        <p v-if="loading" class="text-center">Загрузка...</p>
        <p v-else-if="error" class="text-center">{{ error }}</p>

        <article v-else-if="data.item" class="at_support_article">
            <h1 class="h1"><span class="underline_bottom">{{ data.item.title }}</span></h1>

            <img
                v-if="data.item.preview_image"
                :src="data.item.preview_image"
                :alt="data.item.title"
                class="at_support_article__preview mb-4"
            >

            <p v-if="data.item.description" class="lead text-center mb-4">{{ data.item.description }}</p>

            <div class="prose max-w-none" v-html="data.item.content_html || ''" />

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
