<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { fetchJson } from '../services/api';
import { resolveMediaUrl } from '../utils/media';
import ProductCardSlider from '../components/ProductCardSlider.vue';
import SiteBreadcrumbs from '../components/SiteBreadcrumbs.vue';

const PER_PAGE = 12;

const route = useRoute();
const loading = ref(true);
const loadingMore = ref(false);
const error = ref('');

const category = ref(null);
const breadcrumbs = computed(() => category.value?.breadcrumbs ?? []);

const subcategoriesItems = ref([]);
const subMeta = ref({ current_page: 1, last_page: 1, per_page: PER_PAGE, total: 0 });

const productsItems = ref([]);
const prodMeta = ref({ current_page: 1, last_page: 1, per_page: PER_PAGE, total: 0 });

const subSentinel = ref(null);
const prodSentinel = ref(null);

let observer = null;

const showSubBlock = computed(() => subMeta.value.total > 0);
const showProdBlock = computed(() => prodMeta.value.total > 0);

const canLoadMoreSub = computed(
    () => subMeta.value.current_page < subMeta.value.last_page && !loadingMore.value && !loading.value,
);
const canLoadMoreProd = computed(
    () => prodMeta.value.current_page < prodMeta.value.last_page && !loadingMore.value && !loading.value,
);

function disconnectObserver() {
    observer?.disconnect();
    observer = null;
}

function observeSentinel() {
    disconnectObserver();

    const obs = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (!entry.isIntersecting) {
                    continue;
                }
                if (entry.target === subSentinel.value && canLoadMoreSub.value) {
                    void loadMore('sub');
                } else if (entry.target === prodSentinel.value && canLoadMoreProd.value) {
                    void loadMore('prod');
                }
            }
        },
        { root: null, rootMargin: '160px', threshold: 0 },
    );
    observer = obs;

    if (subSentinel.value && canLoadMoreSub.value) {
        obs.observe(subSentinel.value);
    }
    if (prodSentinel.value && canLoadMoreProd.value) {
        obs.observe(prodSentinel.value);
    }
}

async function load() {
    loading.value = true;
    error.value = '';
    disconnectObserver();

    const slug = route.params.categorySlug;
    const qs = new URLSearchParams({
        subcategories_page: '1',
        products_page: '1',
        per_page: String(PER_PAGE),
    });

    try {
        const data = await fetchJson(`/api/catalog/${slug}?${qs.toString()}`);
        category.value = data.category ?? null;
        document.title = `${category.value?.name ?? 'Категория'} | Atlantic Group`;

        subcategoriesItems.value = data.subcategories?.items ?? [];
        subMeta.value = data.subcategories?.meta ?? {
            current_page: 1,
            last_page: 1,
            per_page: PER_PAGE,
            total: 0,
        };

        productsItems.value = data.products?.items ?? [];
        prodMeta.value = data.products?.meta ?? {
            current_page: 1,
            last_page: 1,
            per_page: PER_PAGE,
            total: 0,
        };
    } catch (e) {
        error.value = 'Ошибка загрузки данных';
    } finally {
        loading.value = false;
        await nextTick();
        observeSentinel();
    }
}

/**
 * @param {'sub' | 'prod'} kind
 */
async function loadMore(kind) {
    if (loadingMore.value || loading.value) {
        return;
    }
    if (kind === 'sub' && !canLoadMoreSub.value) {
        return;
    }
    if (kind === 'prod' && !canLoadMoreProd.value) {
        return;
    }

    loadingMore.value = true;
    const slug = route.params.categorySlug;
    const qs = new URLSearchParams({
        page: String(kind === 'sub' ? subMeta.value.current_page + 1 : prodMeta.value.current_page + 1),
        per_page: String(PER_PAGE),
    });

    try {
        if (kind === 'sub') {
            const data = await fetchJson(`/api/catalog/${slug}/subcategories?${qs.toString()}`);
            subcategoriesItems.value = subcategoriesItems.value.concat(data.items ?? []);
            if (data.meta) {
                subMeta.value = data.meta;
            }
        } else {
            const data = await fetchJson(`/api/catalog/${slug}/products?${qs.toString()}`);
            productsItems.value = productsItems.value.concat(data.items ?? []);
            if (data.meta) {
                prodMeta.value = data.meta;
            }
        }
    } catch {
        /* тихо: следующий скролл повторит */
    } finally {
        loadingMore.value = false;
        await nextTick();
        observeSentinel();
    }
}

onMounted(load);
watch(() => route.params.categorySlug, load);

onUnmounted(() => {
    disconnectObserver();
});
</script>

<template>
    <section>
        <SiteBreadcrumbs
            :items="[
                { label: 'Главная', to: '/' },
                { label: 'Каталог', to: '/catalog' },
                ...breadcrumbs.map((b, i) => ({
                    label: b.name,
                    to: i < breadcrumbs.length - 1 ? `/catalog/${b.slug}` : null,
                })),
            ]"
        />
        <h1 class="h1"><span class="underline_bottom">{{ category?.name ?? 'Категория' }}</span></h1>
        <p v-if="loading" class="text-center">Загрузка...</p>
        <p v-else-if="error" class="text-center">{{ error }}</p>
        <template v-else>
            <p v-if="!showSubBlock && !showProdBlock" class="text-secondary small mt-3 mb-0">
                В этой категории пока нет подкатегорий и товаров.
            </p>

            <div v-if="showSubBlock" class="at_cat_section">
                <h2 class="at_cat_subheading"><span class="underline_bottom">Подкатегории</span></h2>
                <div v-if="subcategoriesItems.length" class="row g-3 justify-content-center at_card_grid_row">
                    <div
                        v-for="sub in subcategoriesItems"
                        :key="'sub-' + sub.id"
                        class="col-6 col-lg-4"
                    >
                        <article class="at_support_card h-100">
                            <RouterLink :to="`/catalog/${sub.slug}`" class="text-reset text-decoration-none d-flex flex-column h-100">
                                <img
                                    class="at_support_card__image"
                                    :src="resolveMediaUrl(sub.image, '/images/source/normalized/image.png')"
                                    :alt="sub.name"
                                >
                                <div class="at_support_card__body">
                                    <h2 class="at_support_card__title">{{ sub.name }}</h2>
                                    <div class="at_but av_otst_tb">
                                        <span class="at_but_style">Подробнее</span>
                                    </div>
                                </div>
                            </RouterLink>
                        </article>
                    </div>
                </div>
                <div ref="subSentinel" class="at_infinite_sentinel" aria-hidden="true" />
            </div>

            <div v-if="showProdBlock" class="at_cat_section" :class="{ 'mt-5': showSubBlock }">
                <h2 class="at_cat_subheading"><span class="underline_bottom">Товары</span></h2>
                <div v-if="productsItems.length" class="row g-3 justify-content-center at_card_grid_row">
                    <div
                        v-for="item in productsItems"
                        :key="'p-' + item.id"
                        class="col-6 col-lg-4"
                    >
                        <article class="at_support_card h-100">
                            <RouterLink
                                class="text-reset text-decoration-none d-flex flex-column h-100"
                                :to="`/catalog/${item.category?.slug ?? route.params.categorySlug}/${item.slug}`"
                            >
                                <div class="at_support_card__media">
                                    <ProductCardSlider
                                        variant="card"
                                        :images="item.images"
                                        :name="item.name"
                                        fallback="/images/source/normalized/image-3.jpg"
                                    />
                                </div>
                                <div class="at_support_card__body">
                                    <h2 class="at_support_card__title">{{ item.name }}</h2>
                                    <p v-if="item.category?.name" class="at_support_card__description small d-none d-md-block">
                                        {{ item.category.name }}
                                    </p>
                                    <p v-if="item.short_description" class="at_support_card__description d-none d-md-block">
                                        {{ item.short_description }}
                                    </p>
                                    <div class="at_but av_otst_tb">
                                        <span class="at_but_style">Подробнее</span>
                                    </div>
                                </div>
                            </RouterLink>
                        </article>
                    </div>
                </div>
                <p v-else class="text-secondary small mb-4">В этой категории пока нет товаров.</p>
                <div ref="prodSentinel" class="at_infinite_sentinel" aria-hidden="true" />
            </div>

            <p v-if="loadingMore" class="text-center text-secondary small py-3 mb-0">Загрузка…</p>
        </template>
    </section>
</template>
