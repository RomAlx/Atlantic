<script setup>
import { onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { fetchJson } from '../services/api';
import ProductCardSlider from '../components/ProductCardSlider.vue';
import SiteBreadcrumbs from '../components/SiteBreadcrumbs.vue';

const route = useRoute();
const loading = ref(true);
const error = ref('');
const data = ref({ item: null });

const load = async () => {
    loading.value = true;
    error.value = '';

    try {
        data.value = await fetchJson(`/api/catalog/${route.params.categorySlug}/${route.params.productSlug}`);
        document.title = `${data.value.item?.name ?? 'Товар'} | Atlantic Group`;
    } catch (e) {
        error.value = 'Ошибка загрузки данных';
    } finally {
        loading.value = false;
    }
};

onMounted(load);
watch(() => [route.params.categorySlug, route.params.productSlug], load);
</script>

<template>
    <section>
        <SiteBreadcrumbs
            :items="[
                { label: 'Главная', to: '/' },
                { label: 'Каталог', to: '/catalog' },
                { label: data.item?.category?.name || 'Категория', to: data.item?.category?.slug ? `/catalog/${data.item.category.slug}` : null },
                { label: data.item?.name ?? 'Товар' },
            ]"
        />
        <h1 class="h1"><span class="underline_bottom">{{ data.item?.name ?? 'Товар' }}</span></h1>
        <p v-if="loading" class="text-center">Загрузка...</p>
        <p v-else-if="error" class="text-center">{{ error }}</p>
        <div v-else class="at_product_page mx-auto">
            <div class="row justify-content-center g-4 align-items-start">
                <div class="col-lg-6 col-md-10 col-sm-12">
                    <div class="at_img_gal_tov at_img_gal_tov_mob mx-lg-0 mx-auto" style="max-width: 520px">
                        <ProductCardSlider
                            variant="detail"
                            :images="data.item?.images ?? []"
                            :name="data.item?.name ?? ''"
                            fallback="/images/source/normalized/image-5.jpg"
                        />
                    </div>
                </div>
                <div class="col-lg-6 col-md-10 col-sm-12">
                    <div class="at_card_one_tov text-center text-lg-start">
                        <div class="at_zag_tov">{{ data.item?.name }}</div>
                        <p><strong>SKU:</strong> {{ data.item?.sku || '—' }}</p>
                        <p>{{ data.item?.short_description }}</p>
                        <p>{{ data.item?.description }}</p>
                    </div>
                </div>
            </div>

            <div v-if="data.item?.specifications" class="at_product_specs mt-4 pt-2">
                <h2 class="at_product_specs__title">Характеристики</h2>
                <div class="table-responsive at_specs_table_wrap">
                    <table class="table at_specs_table mb-0">
                        <tbody>
                            <tr v-for="(value, key) in data.item.specifications" :key="key">
                                <th scope="row">{{ key }}</th>
                                <td>{{ value }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-if="(data.item?.popular_items ?? []).length" class="mt-5">
                <h2 class="at_product_specs__title">Популярные товары</h2>
                <div class="row g-3">
                    <div v-for="item in data.item.popular_items" :key="`popular-${item.id}`" class="col-6 col-md-4 col-xl-3">
                        <div class="at_card_product_list">
                            <div class="at_card_product_list__media">
                                <ProductCardSlider variant="card" :images="item.images" :name="item.name" fallback="/images/source/normalized/image-3.jpg" />
                            </div>
                            <div class="at_card_product_list__body">
                                <div class="at_card_product_list__title">{{ item.name }}</div>
                                <div class="at_card_product_list__action">
                                    <RouterLink class="at_but_style" :to="`/catalog/${item.category.slug}/${item.slug}`">Подробнее</RouterLink>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="(data.item?.related_items ?? []).length" class="mt-5">
                <h2 class="at_product_specs__title">Связанные товары</h2>
                <div class="row g-3">
                    <div v-for="item in data.item.related_items" :key="`related-${item.id}`" class="col-6 col-md-4 col-xl-3">
                        <div class="at_card_product_list">
                            <div class="at_card_product_list__media">
                                <ProductCardSlider variant="card" :images="item.images" :name="item.name" fallback="/images/source/normalized/image-3.jpg" />
                            </div>
                            <div class="at_card_product_list__body">
                                <div class="at_card_product_list__title">{{ item.name }}</div>
                                <div class="at_card_product_list__action">
                                    <RouterLink class="at_but_style" :to="`/catalog/${item.category.slug}/${item.slug}`">Подробнее</RouterLink>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
