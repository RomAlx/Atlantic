<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { fetchJson } from '../services/api';
import HomeBannersCarousel from '../components/HomeBannersCarousel.vue';
import { resolveMediaUrl } from '../utils/media';

const loading = ref(true);
const error = ref('');
const data = ref({ categories: [], products: [], banners: [], home_content: null });

/** На узком экране показываем 2 категории вместо 3 */
const isNarrowHome = ref(false);
let homeMq;

const syncHomeWidth = () => {
    isNarrowHome.value = typeof window !== 'undefined' && window.matchMedia('(max-width: 767.98px)').matches;
};

const homeCatalogCategories = computed(() => {
    const list = data.value.categories ?? [];
    const max = isNarrowHome.value ? 2 : 3;
    return list.slice(0, max);
});

const load = async () => {
    loading.value = true;
    error.value = '';

    try {
        data.value = await fetchJson('/api/home');
        document.title = 'Главная | Atlantic Group';
    } catch (e) {
        error.value = 'Ошибка загрузки данных';
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    syncHomeWidth();
    homeMq = window.matchMedia('(max-width: 767.98px)');
    homeMq.addEventListener('change', syncHomeWidth);
    load();
});

onUnmounted(() => {
    homeMq?.removeEventListener('change', syncHomeWidth);
});
</script>

<template>
    <section>
        <HomeBannersCarousel :items="data.banners || []" />
        <div class="container py-5" v-if="!loading && !error">
            <div class="h1"><span class="underline_bottom">Каталог продукции</span></div>
            <div class="row g-3 justify-content-center at_card_grid_row">
                <div v-for="item in homeCatalogCategories" :key="item.id" class="col-6 col-lg-4">
                    <article class="at_support_card h-100">
                        <RouterLink :to="`/catalog/${item.slug}`" class="text-reset text-decoration-none d-flex flex-column h-100">
                            <img
                                class="at_support_card__image"
                                :src="resolveMediaUrl(item.image, '/images/original/at_img_cat_home.png')"
                                :alt="item.name"
                            >
                            <div class="at_support_card__body">
                                <h2 class="at_support_card__title">{{ item.name }}</h2>
                                <div class="at_but av_otst_tb">
                                    <span class="at_but_style">Подробнее</span>
                                </div>
                            </div>
                        </RouterLink>
                    </article>
                </div>
            </div>

            <div class="h1 mt-3"><span class="underline_bottom">О компании</span></div>
            <div class="row justify-content-center align-items-center mb-5 text-center g-4">
                <div class="col-md-5 mb-3 mb-md-0">
                    <img :src="'/images/original/at_img_about_home.png'" alt="О компании" class="img-fluid rounded-3 mx-auto d-block">
                </div>
                <div class="col-md-5">
                    <p class="small">{{ data.home_content?.about_paragraph_1 ?? '' }}</p>
                    <p class="small">{{ data.home_content?.about_paragraph_2 ?? '' }}</p>
                </div>
            </div>

            <div class="row text-center mb-5">
                <div class="col-md-4 mb-3">
                    <img :src="'/images/original/at_ico_preim_1.png'" alt="">
                    <p class="small mt-2">Большой ассортимент материалов</p>
                </div>
                <div class="col-md-4 mb-3">
                    <img :src="'/images/original/at_ico_preim_2.png'" alt="">
                    <p class="small mt-2">Профессиональная техническая поддержка</p>
                </div>
                <div class="col-md-4 mb-3">
                    <img :src="'/images/original/at_ico_preim_3.png'" alt="">
                    <p class="small mt-2">Высокое качество продукции</p>
                </div>
            </div>

            <div class="h1"><span class="underline_bottom">Информация для клиентов</span></div>
            <div class="row justify-content-center">
                <div
                    v-for="(block, idx) in (data.home_content?.client_blocks || []).slice(0, 4)"
                    :key="idx"
                    class="col-lg-5 col-md-6 mb-4"
                >
                    <div class="at_box_one_st_ekran">
                        <div class="row align-items-center justify-content-center text-center text-md-start g-2">
                            <div class="col-6 col-md-5">
                                <img :src="`/images/original/at_img_st_home_${idx + 1}.png`" class="img-fluid rounded-3 mx-auto d-block" alt="">
                            </div>
                            <div class="col-12 col-md-7">
                                <div class="at_st_zag text-center text-md-start">{{ block.title }}</div>
                                <p class="small">{{ block.text }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <p v-else-if="loading" class="text-center py-5">Загрузка...</p>
        <p v-else class="text-center py-5">{{ error }}</p>
    </section>
</template>
