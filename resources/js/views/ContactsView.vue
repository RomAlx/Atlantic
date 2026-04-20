<script setup>
import { computed, onMounted, ref } from 'vue';
import { fetchJson } from '../services/api';
import SocialIcon from '../components/SocialIcon.vue';
import SiteBreadcrumbs from '../components/SiteBreadcrumbs.vue';
import YandexAddressMap from '../components/YandexAddressMap.vue';

const loading = ref(true);
const error = ref('');
const data = ref({ item: null });

const item = computed(() => data.value?.item);

const phoneRows = computed(() => {
    const list = item.value?.phones;
    if (Array.isArray(list) && list.length > 0) {
        return list.filter((p) => p && typeof p.number === 'string' && p.number.trim().length > 0);
    }
    const legacy = item.value?.phone;
    if (typeof legacy === 'string' && legacy.trim().length > 0) {
        return [{ label: '', number: legacy.trim() }];
    }
    return [];
});

const telHref = (number) => {
    if (!number || typeof number !== 'string') {
        return null;
    }
    const digits = number.replace(/[^\d+]/g, '');
    return digits.length ? digits : null;
};

const socialLinks = computed(() => {
    const list = item.value?.social_links;
    if (Array.isArray(list) && list.length > 0) {
        return list.filter((row) => row && typeof row.url === 'string' && row.url.trim().length > 0);
    }
    return [];
});

const hasMapsApiKey = computed(() => {
    const key = String(globalThis?.__YANDEX_MAPS_API_KEY__ ?? '').trim();
    return key.length > 0;
});

onMounted(async () => {
    loading.value = true;
    error.value = '';
    try {
        data.value = await fetchJson('/api/contacts');
        document.title = 'Контакты | Atlantic Group';
    } catch (e) {
        error.value = e?.message || 'Не удалось загрузить контакты';
        document.title = 'Контакты | Atlantic Group';
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <section class="at_contacts_page">
        <SiteBreadcrumbs :items="[{ label: 'Главная', to: '/' }, { label: 'Контакты' }]" />
        <h1 class="h1"><span class="underline_bottom">Контакты</span></h1>
        <p v-if="loading" class="text-center">Загрузка...</p>
        <p v-else-if="error" class="text-center text-danger">{{ error }}</p>
        <div v-else-if="!item" class="text-center text-secondary">Данные компании пока не заполнены.</div>
        <div v-else class="at_contacts_body mx-auto">
            <p v-if="item.company_name" class="fw-bold fs-5 text-center mb-4">{{ item.company_name }}</p>
            <div v-if="item.address" class="mb-3 text-center">
                <span class="text-secondary d-block small">Адрес</span>
                {{ item.address }}
                <div v-if="hasMapsApiKey" class="mt-3">
                    <YandexAddressMap :address="item.address" :height="280" />
                </div>
            </div>
            <div v-if="item.warehouse_address" class="mb-3 text-center">
                <span class="text-secondary d-block small">Склад</span>
                {{ item.warehouse_address }}
                <div v-if="hasMapsApiKey" class="mt-3">
                    <YandexAddressMap :address="item.warehouse_address" :height="280" />
                </div>
            </div>
            <div v-if="phoneRows.length" class="mb-3 text-center">
                <span class="text-secondary d-block small mb-2">Телефоны</span>
                <template v-for="(row, idx) in phoneRows" :key="'ph-' + idx">
                    <p class="mb-1">
                        <span v-if="row.label" class="small text-secondary d-block">{{ row.label }}</span>
                        <a :href="telHref(row.number) ? `tel:${telHref(row.number)}` : '#'">{{ row.number }}</a>
                    </p>
                </template>
            </div>
            <div v-if="item.email" class="mb-4 text-center">
                <span class="text-secondary d-block small">Email</span>
                <a :href="`mailto:${item.email}`" class="at_link_dark">{{ item.email }}</a>
            </div>
            <div v-if="socialLinks.length" class="d-flex flex-wrap gap-2 justify-content-center align-items-center pt-2">
                <a
                    v-for="(row, idx) in socialLinks"
                    :key="'soc-' + idx"
                    :href="row.url"
                    class="at_social_link"
                    target="_blank"
                    rel="noopener noreferrer"
                    :title="row.label || row.network"
                >
                    <SocialIcon :network="row.network || 'other'" :label="row.label || row.network" />
                </a>
            </div>
        </div>
    </section>
</template>
