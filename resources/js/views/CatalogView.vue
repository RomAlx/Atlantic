<script setup>
import { onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { fetchJson } from '../services/api';
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
        <h1 class="h1"><span class="underline_bottom">Каталог продукции</span></h1>
        <p v-if="loading" class="text-center">Загрузка...</p>
        <p v-else-if="error" class="text-center">{{ error }}</p>
        <div v-else class="row justify-content-center">
            <div v-for="item in data.items" :key="item.id" class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <RouterLink class="av_not_link" :to="`/catalog/${item.slug}`">
                    <div class="at_card_cat">
                        <img
                            class="img-fluid at_preview_fill"
                            :src="resolveMediaUrl(item.image, '/images/source/normalized/image.png')"
                            :alt="item.name"
                        >
                        <div class="at_naim_tov">{{ item.name }}</div>
                        <div v-if="item.subcategories_count || item.products_count" class="small text-secondary px-2 pb-1">
                            <span v-if="item.subcategories_count">{{ item.subcategories_count }} подкатегорий</span>
                            <span v-if="item.subcategories_count && item.products_count"> · </span>
                            <span v-if="item.products_count">{{ item.products_count }} товаров</span>
                        </div>
                        <div class="at_but av_otst_tb"><span class="at_but_style">Подробнее</span></div>
                    </div>
                </RouterLink>
            </div>
        </div>
    </section>
</template>
