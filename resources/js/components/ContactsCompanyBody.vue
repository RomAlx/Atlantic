<script setup>
import { computed } from 'vue';
import SocialIcon from './SocialIcon.vue';
import YandexAddressMap from './YandexAddressMap.vue';

const props = defineProps({
    item: {
        type: Object,
        default: null,
    },
});

function parseContactAddresses(raw) {
    if (raw == null) {
        return [];
    }
    let list = raw;
    if (typeof list === 'string') {
        try {
            list = JSON.parse(list);
        } catch {
            return [];
        }
    }
    if (!Array.isArray(list)) {
        return [];
    }
    return list.filter((row) => row && typeof row.address === 'string' && row.address.trim().length > 0);
}

const standalonePhones = computed(() => {
    const list = props.item?.phones;
    if (Array.isArray(list) && list.length > 0) {
        return list.filter((p) => p && typeof p.number === 'string' && p.number.trim().length > 0);
    }
    const legacy = props.item?.phone;
    if (typeof legacy === 'string' && legacy.trim().length > 0) {
        return [{ label: '', number: legacy.trim() }];
    }
    return [];
});

const locations = computed(() => parseContactAddresses(props.item?.contact_addresses));

const hasMapKey = computed(() => String(globalThis?.__YANDEX_MAPS_API_KEY__ ?? '').trim().length > 0);

const socialLinks = computed(() => {
    const list = props.item?.social_links;
    if (Array.isArray(list) && list.length > 0) {
        return list.filter((row) => row && typeof row.url === 'string' && row.url.trim().length > 0);
    }
    return [];
});

function locationPhoneRows(loc) {
    const raw = loc?.phones;
    if (!Array.isArray(raw)) {
        return [];
    }
    return raw.filter((p) => p && typeof p.number === 'string' && p.number.trim().length > 0);
}

const telHref = (number) => {
    if (!number || typeof number !== 'string') {
        return null;
    }
    const digits = number.replace(/[^\d+]/g, '');
    return digits.length ? digits : null;
};
</script>

<template>
    <div v-if="item" class="at_contacts_body mx-auto">
        <p v-if="item.company_name" class="fw-bold fs-5 text-center mb-4">{{ item.company_name }}</p>

        <div v-if="standalonePhones.length" class="mb-4 text-center">
            <p v-for="(row, idx) in standalonePhones" :key="'st-ph-' + idx" class="mb-1">
                <template v-if="(row.label || '').trim()">
                    {{ (row.label || '').trim() }} — <a :href="telHref(row.number) ? `tel:${telHref(row.number)}` : '#'">{{ row.number }}</a>
                </template>
                <template v-else>
                    <a :href="telHref(row.number) ? `tel:${telHref(row.number)}` : '#'">{{ row.number }}</a>
                </template>
            </p>
        </div>

        <div v-for="(loc, lidx) in locations" :key="'loc-' + lidx" class="mb-4 text-center">
            <p v-if="(loc.title || '').trim()" class="mb-1 fw-bold">{{ (loc.title || '').trim() }}</p>
            <p class="mb-2">{{ loc.address.trim() }}</p>
            <p v-for="(row, idx) in locationPhoneRows(loc)" :key="'loc-ph-' + lidx + '-' + idx" class="mb-1">
                <template v-if="(row.label || '').trim()">
                    {{ (row.label || '').trim() }} — <a :href="telHref(row.number) ? `tel:${telHref(row.number)}` : '#'">{{ row.number }}</a>
                </template>
                <template v-else>
                    <a :href="telHref(row.number) ? `tel:${telHref(row.number)}` : '#'">{{ row.number }}</a>
                </template>
            </p>
            <div v-if="hasMapKey" class="mt-3">
                <YandexAddressMap :address="loc.address.trim()" :height="280" />
            </div>
        </div>

        <div v-if="item.email" class="mb-4 text-center">
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
</template>
