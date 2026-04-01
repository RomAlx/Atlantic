<script setup>
import { onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { fetchJson } from '../services/api';
import ProductCardSlider from '../components/ProductCardSlider.vue';

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
        <h1 class="h1"><span class="underline_bottom">Поиск</span></h1>
        <p v-if="loading" class="text-center">Загрузка...</p>
        <p v-else-if="error" class="text-center">{{ error }}</p>
        <div v-else class="row justify-content-center">
            <div v-for="item in data.items" :key="item.id" class="col-xxl-3 col-xl-3 col-md-6 col-sm-6">
                <div class="at_pop_tov">
                    <RouterLink :to="`/catalog/${item.category.slug}/${item.slug}`" class="text-decoration-none text-reset d-block">
                        <div class="at_pop_tov__media">
                            <ProductCardSlider variant="card" :images="item.images" :name="item.name" fallback="/images/source/normalized/image-4.jpg" />
                        </div>
                        <div class="at_naim_tov_podl">{{ item.name }}</div>
                        <div v-if="item.category?.name" class="at_pop_tov__cat small text-center text-white-50 px-2 pb-2">{{ item.category.name }}</div>
                    </RouterLink>
                </div>
            </div>
        </div>
    </section>
</template>
