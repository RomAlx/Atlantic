<script setup>
import { onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { fetchJson } from '../services/api';
import ProductCardSlider from '../components/ProductCardSlider.vue';
import SiteBreadcrumbs from '../components/SiteBreadcrumbs.vue';

const route = useRoute();
const loading = ref(true);
const error = ref('');
const data = ref({ items: [] });

const load = async () => {
    loading.value = true;
    error.value = '';

    try {
        const query = String(route.query.q ?? '');
        data.value = await fetchJson(`/api/search?q=${encodeURIComponent(query)}`);
        document.title = `Поиск: ${query} | Atlantic Group`;
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
        <SiteBreadcrumbs :items="[{ label: 'Главная', to: '/' }, { label: 'Поиск' }]" />
        <h1 class="h1"><span class="underline_bottom">Поиск</span></h1>
        <p v-if="loading" class="text-center">Загрузка...</p>
        <p v-else-if="error" class="text-center">{{ error }}</p>
        <div v-else class="row g-3 justify-content-center at_card_grid_row">
            <div v-for="item in data.items" :key="item.id" class="col-6 col-lg-4">
                <article class="at_support_card h-100">
                    <RouterLink
                        :to="`/catalog/${item.category.slug}/${item.slug}`"
                        class="text-reset text-decoration-none d-flex flex-column h-100"
                    >
                        <div class="at_support_card__media">
                            <ProductCardSlider variant="card" :images="item.images" :name="item.name" fallback="/images/source/normalized/image-4.jpg" />
                        </div>
                        <div class="at_support_card__body">
                            <h2 class="at_support_card__title">{{ item.name }}</h2>
                            <p v-if="item.category?.name" class="at_support_card__description small d-none d-md-block">{{ item.category.name }}</p>
                            <p v-if="item.short_description" class="at_support_card__description d-none d-md-block">{{ item.short_description }}</p>
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
