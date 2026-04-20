<script setup>
import { onMounted, ref } from 'vue';
import { fetchJson } from '../services/api';
import SiteBreadcrumbs from '../components/SiteBreadcrumbs.vue';

const loading = ref(true);
const error = ref('');
const data = ref({ item: null });

onMounted(async () => {
    loading.value = true;
    error.value = '';
    try {
        data.value = await fetchJson('/api/pages/privacy-consent');
        document.title = `${data.value.item?.seo_title || data.value.item?.title || 'Согласие на обработку персональных данных'} | Atlantic Group`;
    } catch (e) {
        error.value = 'Не удалось загрузить страницу';
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <section class="at_support_article_wrap">
        <SiteBreadcrumbs
            :items="[
                { label: 'Главная', to: '/' },
                { label: data.item?.title || 'Согласие на обработку персональных данных' },
            ]"
        />

        <p v-if="loading" class="text-center">Загрузка...</p>
        <p v-else-if="error" class="text-center">{{ error }}</p>

        <article v-else-if="data.item" class="at_support_article">
            <h1 class="h1">
                <span class="underline_bottom">{{ data.item.title }}</span>
            </h1>

            <div class="prose max-w-none at_md_prose" v-html="data.item.content_html || ''" />
        </article>
    </section>
</template>
