<script setup>
import { onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { fetchJson } from '../services/api';
import SiteBreadcrumbs from '../components/SiteBreadcrumbs.vue';

const route = useRoute();
const router = useRouter();
const loading = ref(true);
const error = ref('');
const data = ref({ query: '', items: [] });
const searchInput = ref(String(route.query.q ?? ''));

const load = async () => {
    loading.value = true;
    error.value = '';

    try {
        const query = String(route.query.q ?? '');
        data.value = await fetchJson(`/api/support?q=${encodeURIComponent(query)}`);
        searchInput.value = query;
        document.title = query
            ? `Техподдержка: ${query} | Atlantic Group`
            : 'Техническая поддержка | Atlantic Group';
    } catch (e) {
        error.value = 'Ошибка загрузки статей';
    } finally {
        loading.value = false;
    }
};

const onSubmit = async () => {
    const q = searchInput.value.trim();
    await router.push({ name: 'support', query: q ? { q } : {} });
};

onMounted(load);
watch(() => route.query.q, load);
</script>

<template>
    <section>
        <SiteBreadcrumbs :items="[{ label: 'Главная', to: '/' }, { label: 'Техподдержка' }]" />
        <h1 class="h1"><span class="underline_bottom">Техническая поддержка</span></h1>

        <form class="at_support_search mb-4" @submit.prevent="onSubmit">
            <input v-model="searchInput" type="search" class="form-control" placeholder="Поиск по статьям" />
            <button class="btn at_btn_style" type="submit">Найти</button>
        </form>

        <p v-if="loading" class="text-center">Загрузка...</p>
        <p v-else-if="error" class="text-center">{{ error }}</p>
        <p v-else-if="!data.items.length" class="text-center text-muted">Статьи не найдены.</p>

        <div v-else class="row g-3">
            <div v-for="item in data.items" :key="item.id" class="col-xl-4 col-md-6">
                <article class="at_support_card h-100">
                    <RouterLink :to="`/support/${item.slug}`" class="text-reset text-decoration-none d-flex flex-column h-100">
                        <img
                            :src="item.preview_image || '/images/source/normalized/image-4.jpg'"
                            :alt="item.title"
                            class="at_support_card__image"
                        >
                        <div class="at_support_card__body">
                            <h2 class="at_support_card__title">{{ item.title }}</h2>
                            <p v-if="item.description" class="at_support_card__description">{{ item.description }}</p>
                            <span class="at_support_card__action">Открыть статью</span>
                        </div>
                    </RouterLink>
                </article>
            </div>
        </div>
    </section>
</template>
