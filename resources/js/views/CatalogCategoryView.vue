<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { fetchJson } from '../services/api';
import { resolveMediaUrl } from '../utils/media';
import ProductCardSlider from '../components/ProductCardSlider.vue';

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

const activeTab = ref('subcategories');

const subSentinel = ref(null);
const prodSentinel = ref(null);

let observer = null;

const showSubBlock = computed(() => subMeta.value.total > 0);
const showProdBlock = computed(() => prodMeta.value.total > 0);
const showTabs = computed(() => showSubBlock.value && showProdBlock.value);

const canLoadMoreSub = computed(
    () => subMeta.value.current_page < subMeta.value.last_page && !loadingMore.value && !loading.value,
);
const canLoadMoreProd = computed(
    () => prodMeta.value.current_page < prodMeta.value.last_page && !loadingMore.value && !loading.value,
);

function pickDefaultTab() {
    if (subMeta.value.total > 0) {
        activeTab.value = 'subcategories';
    } else {
        activeTab.value = 'products';
    }
}

function disconnectObserver() {
    observer?.disconnect();
    observer = null;
}

function observeSentinel() {
    disconnectObserver();
    const el =
        showTabs.value === true
            ? activeTab.value === 'subcategories'
                ? subSentinel.value
                : prodSentinel.value
            : showSubBlock.value && !showProdBlock.value
              ? subSentinel.value
              : showProdBlock.value && !showSubBlock.value
                ? prodSentinel.value
                : null;

    if (!el) {
        return;
    }

    observer = new IntersectionObserver(
        (entries) => {
            if (!entries[0]?.isIntersecting) {
                return;
            }
            void loadMore();
        },
        { root: null, rootMargin: '160px', threshold: 0 },
    );
    observer.observe(el);
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

        pickDefaultTab();
    } catch (e) {
        error.value = 'Ошибка загрузки данных';
    } finally {
        loading.value = false;
        await nextTick();
        observeSentinel();
    }
}

async function loadMore() {
    if (loadingMore.value || loading.value) {
        return;
    }

    const slug = route.params.categorySlug;
    const useSub =
        (showTabs.value && activeTab.value === 'subcategories') ||
        (!showTabs.value && showSubBlock.value && !showProdBlock.value);
    const useProd =
        (showTabs.value && activeTab.value === 'products') ||
        (!showTabs.value && showProdBlock.value && !showSubBlock.value);

    if (useSub && !canLoadMoreSub.value) {
        return;
    }
    if (useProd && !canLoadMoreProd.value) {
        return;
    }

    loadingMore.value = true;
    const qs = new URLSearchParams({
        page: String(useSub ? subMeta.value.current_page + 1 : prodMeta.value.current_page + 1),
        per_page: String(PER_PAGE),
    });

    try {
        if (useSub) {
            const data = await fetchJson(`/api/catalog/${slug}/subcategories?${qs.toString()}`);
            subcategoriesItems.value = subcategoriesItems.value.concat(data.items ?? []);
            if (data.meta) {
                subMeta.value = data.meta;
            }
        } else if (useProd) {
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

watch(activeTab, async () => {
    await nextTick();
    observeSentinel();
});

onUnmounted(() => {
    disconnectObserver();
});
</script>

<template>
    <section>
        <nav v-if="breadcrumbs.length > 1" class="at_cat_breadcrumbs small text-secondary mb-2" aria-label="Навигация по каталогу">
            <template v-for="(b, i) in breadcrumbs" :key="b.id">
                <RouterLink v-if="i < breadcrumbs.length - 1" :to="`/catalog/${b.slug}`" class="text-decoration-none">{{ b.name }}</RouterLink>
                <span v-else>{{ b.name }}</span>
                <span v-if="i < breadcrumbs.length - 1" class="px-1">/</span>
            </template>
        </nav>
        <h1 class="h1"><span class="underline_bottom">{{ category?.name ?? 'Категория' }}</span></h1>
        <p v-if="loading" class="text-center">Загрузка...</p>
        <p v-else-if="error" class="text-center">{{ error }}</p>
        <template v-else>
            <div v-if="showTabs" class="at_cat_tabs mb-4" role="tablist" aria-label="Разделы категории">
                <button
                    type="button"
                    role="tab"
                    class="at_cat_tabs__btn"
                    :class="{ 'at_cat_tabs__btn--active': activeTab === 'subcategories' }"
                    :aria-selected="activeTab === 'subcategories'"
                    @click="activeTab = 'subcategories'"
                >
                    Подкатегории
                    <span class="at_cat_tabs__count">{{ subMeta.total }}</span>
                </button>
                <button
                    type="button"
                    role="tab"
                    class="at_cat_tabs__btn"
                    :class="{ 'at_cat_tabs__btn--active': activeTab === 'products' }"
                    :aria-selected="activeTab === 'products'"
                    @click="activeTab = 'products'"
                >
                    Товары
                    <span class="at_cat_tabs__count">{{ prodMeta.total }}</span>
                </button>
            </div>

            <p v-if="!showSubBlock && !showProdBlock" class="text-secondary small mt-3 mb-0">
                В этой категории пока нет подкатегорий и товаров.
            </p>

            <div
                v-show="showTabs ? activeTab === 'subcategories' : showSubBlock"
                class="at_cat_panel"
                role="tabpanel"
            >
                <h2 v-if="!showTabs && showSubBlock" class="h5 fw-bold mt-2 mb-3">Подкатегории</h2>
                <div v-if="subcategoriesItems.length" class="row justify-content-center">
                    <div
                        v-for="sub in subcategoriesItems"
                        :key="'sub-' + sub.id"
                        class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3"
                    >
                        <RouterLink class="av_not_link" :to="`/catalog/${sub.slug}`">
                            <div class="at_card_cat">
                                <img
                                    class="img-fluid at_preview_fill"
                                    :src="resolveMediaUrl(sub.image, '/images/source/normalized/image.png')"
                                    :alt="sub.name"
                                >
                                <div class="at_naim_tov">{{ sub.name }}</div>
                                <div class="at_but av_otst_tb"><span class="at_but_style">Подробнее</span></div>
                            </div>
                        </RouterLink>
                    </div>
                </div>
                <div ref="subSentinel" class="at_infinite_sentinel" aria-hidden="true" />
                <p v-if="loadingMore && (showTabs ? activeTab === 'subcategories' : showSubBlock)" class="text-center text-secondary small py-3 mb-0">
                    Загрузка…
                </p>
            </div>

            <div
                v-show="showTabs ? activeTab === 'products' : showProdBlock"
                class="at_cat_panel"
                role="tabpanel"
            >
                <h2 v-if="!showTabs && showProdBlock" class="h5 fw-bold mt-4 mb-3">Товары категории</h2>
                <div v-if="productsItems.length" class="row justify-content-center">
                    <div
                        v-for="item in productsItems"
                        :key="'p-' + item.id"
                        class="col-xxl-4 col-xl-4 col-lg-6 col-md-6 col-sm-12 mb-3"
                    >
                        <div class="at_card_product_list">
                            <div class="at_card_product_list__media">
                                <ProductCardSlider
                                    variant="card"
                                    :images="item.images"
                                    :name="item.name"
                                    fallback="/images/source/normalized/image-3.jpg"
                                />
                            </div>
                            <div class="at_card_product_list__body">
                                <div class="at_card_product_list__title">{{ item.name }}</div>
                                <div v-if="item.category?.name" class="at_card_product_list__cat small text-secondary">
                                    {{ item.category.name }}
                                </div>
                                <div class="at_card_product_list__action">
                                    <RouterLink
                                        class="at_but_style"
                                        :to="`/catalog/${item.category?.slug ?? route.params.categorySlug}/${item.slug}`"
                                    >Подробнее</RouterLink>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-else-if="showProdBlock" class="text-secondary small mb-4">В этой категории пока нет товаров.</p>
                <div ref="prodSentinel" class="at_infinite_sentinel" aria-hidden="true" />
                <p v-if="loadingMore && (showTabs ? activeTab === 'products' : showProdBlock)" class="text-center text-secondary small py-3 mb-0">
                    Загрузка…
                </p>
            </div>
        </template>
    </section>
</template>
