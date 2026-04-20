<script setup>
import { onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { fetchJson } from '../services/api';
import SiteBreadcrumbs from '../components/SiteBreadcrumbs.vue';
import { resolveMediaUrl } from '../utils/media';

const route = useRoute();
const loading = ref(true);
const error = ref('');
const data = ref({ items: [] });

const load = async () => {
    loading.value = true;
    error.value = '';

    try {
        const query = String(route.query.q ?? '');
        data.value = await fetchJson(`/api/catalog${query ? `?q=${encodeURIComponent(query)}` : ''}`);
        document.title = 'Каталог | Atlantic Group';
    } catch (e) {
        error.value = 'Ошибка загрузки данных';
    } finally {
        loading.value = false;
    }
};

onMounted(load);
watch(() => route.query.q, load);
</script>

<template>
    <section>
        <SiteBreadcrumbs :items="[{ label: 'Главная', to: '/' }, { label: 'Каталог' }]" />
        <h1 class="h1"><span class="underline_bottom">Каталог продукции</span></h1>
        <p v-if="loading" class="text-center">Загрузка...</p>
        <p v-else-if="error" class="text-center">{{ error }}</p>
        <div v-else class="row g-3 justify-content-center at_card_grid_row">
            <div v-for="item in data.items" :key="item.id" class="col-6 col-lg-4">
                <article class="at_support_card h-100">
                    <RouterLink :to="`/catalog/${item.slug}`" class="text-reset text-decoration-none d-flex flex-column h-100">
                        <img
                            class="at_support_card__image"
                            :src="resolveMediaUrl(item.image, '/images/source/normalized/image.png')"
                            :alt="item.name"
                        >
                        <div class="at_support_card__body">
                            <h2 class="at_support_card__title">{{ item.name }}</h2>
                            <p
                                v-if="item.subcategories_count || item.products_count"
                                class="at_support_card__description d-none d-md-block"
                            >
                                <span v-if="item.subcategories_count">{{ item.subcategories_count }} подкатегорий</span>
                                <span v-if="item.subcategories_count && item.products_count"> · </span>
                                <span v-if="item.products_count">{{ item.products_count }} товаров</span>
                            </p>
                            <div class="at_but av_otst_tb">
                                <span class="at_but_style">Подробнее</span>
                            </div>
                        </div>
                    </RouterLink>
                </article>
            </div>
        </div>
    </section>
</template>
