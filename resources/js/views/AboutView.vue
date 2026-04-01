<script setup>
import { onMounted, ref } from 'vue';
import { fetchJson } from '../services/api';

const loading = ref(true);
const error = ref('');
const data = ref({ item: null });

onMounted(async () => {
    loading.value = true;
    error.value = '';

    try {
        data.value = await fetchJson('/api/pages/about');
        document.title = `${data.value.item?.title ?? 'О компании'} | Atlantic Group`;
    } catch (e) {
        data.value = { item: { content: '<p>Информация о компании скоро будет опубликована.</p>' } };
        document.title = 'О компании | Atlantic Group';
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <section>
        <h1 class="mb-4 text-3xl font-semibold">О компании</h1>
        <p v-if="loading">Загрузка...</p>
        <p v-else-if="error" class="text-red-600">{{ error }}</p>
        <div v-else class="prose max-w-none" v-html="data.item?.content || '<p>Контент пока не заполнен.</p>'"></div>
    </section>
</template>
